<?php

namespace App\Service;

use App\Error\Exception\ApiValidationException;
use App\Model\Table\AddressesTable;
use App\Model\Table\VisitsTable;
use App\Model\Table\WorkdaysTable;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

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

    public function createVisit(array $data)
    {
        $connection = ConnectionManager::get('default');
        return $connection->transactional(function () use ($data) {
            // 1. Validate postal_code and create an Address if correct
            $addressData = $this->PostalCodeValidator->validateAndFetchAddress(
                $data
            );

            $address = $this->Addresses->newEntity($addressData);
            if ($address->getErrors()) {
                throw new ApiValidationException('Erro ao criar endereço', $address->getErrors());
            }
            $this->Addresses->saveOrFail($address);

            // 2. Find or create a new Workday
            $existingWorkday = $this->Workdays->find()
                ->where(['date' => $data['date']])
                ->first();

            $duration = $this->calculateVisitDuration($data['forms'], $data['products']);

            if (!$existingWorkday) {
                if ($duration > 480) {
                    throw new ApiValidationException('A duração total excede o limite de 8 horas.');
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
                    throw new ApiValidationException('A duração total excede o limite de 8 horas.');
                }
                // Atualização será feita depois via evento
            }

            // 3. Create a visit
            $visitEntity = $this->Visits->newEntity([
                'date' => $data['date'],
                'forms' => $data['forms'],
                'products' => $data['products'],
                'status' => $data['status'],
                'address_id' => $address->id,
                'workday_id' => $workday->id,
            ]);
            $this->Visits->saveOrFail($visitEntity);

            return $visitEntity;
        });
    }

    private function calculateVisitDuration(int $forms, int $products): int
    {
        // Products: 5 min, Forms: 15 min
        return ($forms * 15 + $products * 5); // minutes
    }
}
