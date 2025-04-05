<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * WorkdaysFixture
 */
class WorkdaysFixture extends TestFixture
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
                'visits' => 1,
                'completed' => 1,
                'duration' => 1,
                'created_at' => 1743811796,
                'updated_at' => 1743811796,
            ],
        ];
        parent::init();
    }
}
