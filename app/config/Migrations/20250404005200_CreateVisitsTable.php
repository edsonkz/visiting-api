<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateVisitsTable extends AbstractMigration
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
        $table = $this->table('visits', [
            'collation' => 'utf8mb4_unicode_ci',
            'encoding' => 'utf8mb4',
        ]);
        $table
            ->addColumn('date', 'date', ['null' => false])
            ->addColumn('status', 'string', ['limit' => 50, 'null' => false])
            ->addColumn('completed', 'boolean', ['default' => false, 'null' => false])
            ->addColumn('forms', 'integer', ['null' => false])
            ->addColumn('products', 'integer', ['null' => false])
            ->addColumn('address_id', 'integer', ['null' => false])
            ->addColumn('workday_id', 'integer', ['null' => false])
            ->addForeignKey('address_id', 'addresses', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION']) // 1-1 Relation with Addresses table
            ->addForeignKey('workday_id', 'workdays', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION']) // N-1 Relation with Workdays table
            ->addTimestamps()
            ->create();
    }
}
