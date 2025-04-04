<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateAddressesTable extends AbstractMigration
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

        $table = $this->table('addresses');

        $table
            ->addColumn('postal_code', 'string', ['limit' => 8, 'null' => false])
            ->addColumn('state', 'string', ['limit' => 8, 'null' => false])
            ->addColumn('city', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('sublocality', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('street', 'string', ['limit' => 150, 'null' => false])
            ->addColumn('street_number', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('complement', 'string', ['limit' => 255, 'default' => '', 'null' => false])
            ->addTimestamps(); // Add columns created_at and updated_at

        $table->create();
    }
}
