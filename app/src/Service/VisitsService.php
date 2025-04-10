<?php

namespace App\Service;

use App\Error\Exception\ApiValidationException;
use App\Model\Table\AddressesTable;
use App\Model\Table\VisitsTable;
use App\Model\Table\WorkdaysTable;
use App\Utility\TextNormalizer;

use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\FrozenDate;

class VisitsService
{
    // Tables
    protected VisitsTable $Visits;
    protected WorkdaysTable $Workdays;
    protected AddressesTable $Addresses;

    //Services
    protected PostalCodeValidatorService $PostalCodeValidator;

    public function __construct()
    {
        $this->Visits = TableRegistry::getTableLocator()->get('Visits');
        $this->Workdays = TableRegistry::getTableLocator()->get('Workdays');
        $this->Addresses = TableRegistry::getTableLocator()->get('Addresses');
        $this->PostalCodeValidator = new PostalCodeValidatorService();
    }

    protected function normalizeText(string $text): string
    {
        return TextNormalizer::normalize($text);
    }

    public function create(array $data)
    {
        $connection = ConnectionManager::get('default');
        return $connection->transactional(function () use ($data) {
            // 1. Validate postal_code and create an Address if correct
            $addressData = $this->PostalCodeValidator->validateAndFetchAddress(
                $data
            );

            $address = $this->Addresses->newEntity($addressData);
            if ($address->getErrors()) {
                throw new ApiValidationException('Erro ao criar endereço', $address->getErrors(), 400);
            }
            $this->Addresses->saveOrFail($address);

            // 2. Find or create a new Workday
            $existingWorkday = $this->Workdays->find()
                ->where(['date' => $data['date']])
                ->first();

            $duration = $this->calculateVisitDuration($data['forms'], $data['products']);

            if (!$existingWorkday) {
                if ($duration > 480) {
                    throw new ApiValidationException('Limite de horas atingido', [], 400);
                }

                $workday = $this->Workdays->newEntity([
                    'date' => $data['date'],
                    'visits' => 1,
                    'completed' => (isset($data['completed']) && $data['completed']) ? 1 : 0,
                    'duration' => $duration,
                ]);
                $this->Workdays->saveOrFail($workday);
            } else {
                $workday = $existingWorkday;

                if ($workday->duration + $duration > 480) {
                    throw new ApiValidationException('Limite de horas atingido', [], 400);
                }

                $workday->visits += 1;
                $workday->duration += $duration;

                if (!empty($data['completed']) && $data['completed']) {
                    $workday->completed += 1;
                }

                $this->Workdays->saveOrFail($workday);
            }

            // 3. Create a visit
            $visitEntity = $this->Visits->newEntity([
                'date' => $data['date'],
                'forms' => $data['forms'],
                'products' => $data['products'],
                'status' => $this->normalizeText($data['status']),
                'address_id' => $address->id,
                'workday_id' => $workday->id,
                'completed' => $data['completed'] ?? false
            ]);

            $this->Visits->saveOrFail($visitEntity);

            return $visitEntity;
        });
    }

    public function findByDate(string $date)
    {
        $visits = $this->Visits->find()->contain(['Addresses'])->where(['date' => $date])->all();

        return $visits;
    }

    public function update(array $data, int $id)
    {
        $connection = ConnectionManager::get('default');

        return $connection->transactional(function () use ($data, $id) {
            // Check if the id is valid
            $visit = $this->Visits->find()->contain(['Addresses', 'Workdays'])->where(['Visits.id' => $id])->first();
            if (!$visit) {
                throw new ApiValidationException('Visita não encontrada com esse id', [], 400);
            }

            $address = null;
            $oldAddressId = $visit->address_id;
            // Create ne address if data received
            if (!empty($data['address'])) {
                // Use old postal_code if not given
                if (!isset($data['address']['postal_code'])) {
                    $data['address']['postal_code'] = $visit->address->postal_code;
                }
                // Use old street_number if not given
                if (!isset($data['address']['street_number'])) {
                    $data['address']['street_number'] = $visit->address->street_number;
                }
                // Validate postal_code and create an Address if correct
                $addressData = $this->PostalCodeValidator->validateAndFetchAddress(
                    $data['address']
                );

                $address = $this->Addresses->newEntity($addressData);
                if ($address->getErrors()) {
                    throw new ApiValidationException('Erro ao criar endereço', $address->getErrors(), 400);
                }
                $this->Addresses->saveOrFail($address);
            }

            $frozenDate = null;
            // Workday empty object
            $workday = null;
            // Auxiliary variables to calculate new duration if needed
            $new_forms = !empty($data['forms']) ? $data['forms'] : $visit->forms;
            $new_products = !empty($data['products']) ? $data['products'] : $visit->products;

            if (!empty($data['date']))
                $frozenDate = new FrozenDate($data['date']);

            // if there is a new date and is different from the old one
            if ($frozenDate !== null && !$frozenDate->eq($visit->date)) {
                // Get old workday then decrease duration, visit and completed because the visit was set to another day
                $oldWorkday = $visit->workday;
                $oldWorkday->duration -= $this->calculateVisitDuration($visit->forms, $visit->products);
                $oldWorkday->visits -= 1;

                if ($visit->completed)
                    $oldWorkday->completed -= 1;

                $this->Workdays->saveOrFail($oldWorkday);

                // Find or create a new Workday
                $existingWorkday = $this->Workdays->find()
                    ->where(['date' => $data['date']])
                    ->first();

                $duration = $this->calculateVisitDuration($new_forms, $new_products);

                if (!$existingWorkday) {
                    if ($duration > 480) {
                        throw new ApiValidationException('Limite de horas atingido', [], 400);
                    }

                    $workday = $this->Workdays->newEntity([
                        'date' => $data['date'],
                        'visits' => 1,
                        'completed' => (isset($data['completed']) && $data['completed']) ? 1 : 0,
                        'duration' => $duration,
                    ]);
                    $this->Workdays->saveOrFail($workday);
                } else {
                    $workday = $existingWorkday;

                    if ($workday->duration + $duration > 480) {
                        throw new ApiValidationException('Limite de horas atingido', [], 400);
                    }

                    $workday->visits += 1;
                    $workday->duration += $duration;

                    if (!empty($data['completed']) && $data['completed']) {
                        $workday->completed += 1;
                    }

                    $this->Workdays->saveOrFail($workday);
                }
            } else {
                // Workday was not changed
                $workday = $visit->workday;
                // If forms and products was changed, update duration on Workday
                if ($visit->forms !== $new_forms || $visit->products !== $new_products) {
                    $new_duration = $this->calculateVisitDuration($new_forms, $new_products);
                    $current_duration = $this->calculateVisitDuration($visit->forms, $visit->products);

                    $actual_duration = $new_duration - $current_duration;

                    $workday->duration += $actual_duration;

                    if ($workday->duration > 480) {
                        throw new ApiValidationException('Limite de horas atingido', [], 400);
                    }
                }

                // Check if completed was changed
                if (array_key_exists('completed', $data) && $data['completed'] !== $visit->completed) {
                    $workday->completed += $data['completed'] ? 1 : -1;
                }

                $this->Workdays->saveOrFail($workday);
            }

            $updateData = [
                'date' => $data['date'] ?? $visit->date,
                'status' => $data['status'] ?? $visit->status,
                'forms' => isset($data['forms']) ? (int)$data['forms'] : $visit->forms,
                'products' => $data['products'] ?? $visit->products,
                'completed' => $data['completed'] ?? $visit->completed,
                'address_id' => $address ? $address->id : $visit->address_id,
                'workday_id' => $workday ? $workday->id : $visit->workday_id,
            ];

            $this->Visits->patchEntity($visit, $updateData, [
                'fields' => ['status', 'forms', 'products', 'completed', 'address_id', 'date', 'workday_id']
            ]);

            $this->Visits->saveOrFail($visit);

            // Safely, delete oldAddress
            if ($updateData['address_id'] !== $oldAddressId) {
                $oldAddress = $this->Addresses->get($oldAddressId);
                $this->Addresses->delete($oldAddress);
            }

            $visit->address = $address ? $address : $visit->address;
            $visit->workday = $workday ? $workday : $visit->workday;
            return $visit;
        });
    }

    private function calculateVisitDuration(int $forms, int $products): int
    {
        // Products: 5 min, Forms: 15 min
        return ($forms * 15 + $products * 5); // minutes
    }
}
