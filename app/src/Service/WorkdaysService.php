<?php

namespace App\Service;

use App\Model\Table\VisitsTable;
use App\Model\Table\WorkdaysTable;

use Cake\ORM\TableRegistry;
use Cake\I18n\FrozenDate;
use Cake\Datasource\ConnectionManager;

class WorkdaysService
{
    protected WorkdaysTable $Workdays;
    protected VisitsTable $Visits;

    public function __construct()
    {
        $this->Workdays = TableRegistry::getTableLocator()->get('Workdays');
        $this->Visits = TableRegistry::getTableLocator()->get('Visits');
    }

    public function findAll()
    {
        $workdays = $this->Workdays->find()->all();

        return $workdays;
    }

    public function close(string $date)
    {
        $connection = ConnectionManager::get('default');

        return $connection->transactional(function () use ($date) {
            $visits = $this->Visits->find()
                ->where(['date' => $date, 'completed' => false])
                ->order(['created_at' => 'ASC'])
                ->all();

            if ($visits->isEmpty()) {
                return; // No visits on that date
            }

            // Convert to iterator array
            $remainingVisits = iterator_to_array($visits);
            $currentDate = $this->getNextWorkdayDate($date);
            $showVisits = [];

            while (!empty($remainingVisits)) {
                // Seach next workday
                $workday = $this->Workdays->find()
                    ->where(['date' => $currentDate])
                    ->first();

                // If don't exists, create a new one
                if (!$workday) {
                    // Calculate current totalDuration
                    $totalDuration = array_reduce($remainingVisits, function ($carry, $visit) {
                        return $carry + $this->calculateVisitDuration($visit->forms, $visit->products);
                    }, 0);

                    $workday = $this->Workdays->newEntity([
                        'date' => $currentDate,
                        'completed' => 0,
                        'visits' => count($remainingVisits),
                        'duration' => $totalDuration,
                    ]);

                    $this->Workdays->save($workday);

                    foreach ($remainingVisits as $visit) {
                        $visit->workday_id = $workday->id;
                        $visit->date = $currentDate;
                    }

                    $this->Visits->saveMany($remainingVisits);
                    $showVisits = $remainingVisits;
                    break;
                }

                // If exists, insert until the avaliable limit used
                $availableMinutes = 480 - (int) $workday->duration;
                $totalUsed = (int) $workday->duration;
                $chunk = [];

                foreach ($remainingVisits as $i => $visit) {
                    $duration = $this->calculateVisitDuration($visit->forms, $visit->products);
                    // limit reached
                    if ($duration > $availableMinutes) {
                        break;
                    }

                    $visit->workday_id = $workday->id;
                    $visit->date = $currentDate;

                    $chunk[] = $visit;
                    $availableMinutes -= $duration;
                    $totalUsed += $duration;

                    $showVisits[] = $remainingVisits[$i];
                    unset($remainingVisits[$i]);
                }

                if (!empty($chunk)) {
                    $this->Visits->saveMany($chunk);
                    $this->updateWorkdayStats($workday->date->format('Y-m-d'));
                }

                $currentDate = $this->getNextWorkdayDate($currentDate);
            }

            $this->updateWorkdayStats($date);
            return $showVisits;
        });
    }

    private function updateWorkdayStats(string $date): void
    {
        $workday = $this->Workdays->find()->contain(['Visits'])->where(['date' => $date])->first();

        $duration = 0;
        $completed = 0;
        $totalVisits = count($workday->related_visits);
        foreach ($workday->related_visits as $visit) {
            $duration += $this->calculateVisitDuration($visit->forms, $visit->products);
            if ($visit->completed) {
                $completed++;
            }
        }

        $workday->duration = $duration;
        $workday->completed = $completed;
        $workday->visits = $totalVisits;

        $this->Workdays->saveOrFail($workday);
    }

    private function getNextWorkdayDate($date): string
    {
        if (!$date instanceof FrozenDate) {
            $date = new FrozenDate($date);
        }

        do {
            $date = $date->addDay();
        } while ($date->isWeekend());

        return $date->format('Y-m-d');
    }

    private function calculateVisitDuration(int $forms, int $products): int
    {
        // Products: 5 min, Forms: 15 min
        return ($forms * 15 + $products * 5); // minutes
    }
}
