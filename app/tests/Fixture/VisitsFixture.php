<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * VisitsFixture
 */
class VisitsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'date' => '2025-04-05',
                'status' => 'Lorem ipsum dolor sit amet',
                'completed' => 1,
                'forms' => 1,
                'products' => 1,
                'duration' => 1,
                'address_id' => 1,
                'workday_id' => 1,
                'created_at' => 1743812682,
                'updated_at' => 1743812682,
            ],
        ];
        parent::init();
    }
}
