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

        $table = $this->table('addresses', [
            'collation' => 'utf8mb4_unicode_ci',
            'encoding' => 'utf8mb4',
        ]);

        $utf8Collation = ['collation' => 'utf8mb4_unicode_ci'];

        $table
            ->addColumn('postal_code', 'string', ['limit' => 8, 'null' => false] + $utf8Collation)
            ->addColumn('state', 'string', ['limit' => 8, 'null' => false] + $utf8Collation)
            ->addColumn('city', 'string', ['limit' => 100, 'null' => false] + $utf8Collation)
            ->addColumn('sublocality', 'string', ['limit' => 100, 'null' => false] + $utf8Collation)
            ->addColumn('street', 'string', ['limit' => 150, 'null' => false] + $utf8Collation)
            ->addColumn('street_number', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('complement', 'string', ['limit' => 255, 'default' => '', 'null' => false] + $utf8Collation)
            ->addTimestamps(); // Add columns created_at and updated_at

        $table->create();
    }
}
