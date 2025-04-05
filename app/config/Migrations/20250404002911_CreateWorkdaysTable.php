<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateWorkdaysTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('workdays', [
            'collation' => 'utf8mb4_unicode_ci',
            'encoding' => 'utf8mb4',
        ]);

        $table
            ->addColumn('date', 'date', ['null' => false])
            ->addColumn('visits', 'integer', ['default' => 0, 'null' => false])
            ->addColumn('completed', 'integer', ['default' => 0, 'null' => false])
            ->addColumn('duration', 'integer', ['default' => 0, 'null' => false, 'comment' => 'Total duration on minutes'])
            ->addIndex(['date'], ['unique' => true]) // Date column should be unique
            ->addTimestamps(); // Add columns created_at and updated_at

        $table->create();
    }
}
