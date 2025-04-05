<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AddressesFixture
 */
class AddressesFixture extends TestFixture
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
                'postal_code' => 'Lorem ',
                'state' => 'Lorem ',
                'city' => 'Lorem ipsum dolor sit amet',
                'sublocality' => 'Lorem ipsum dolor sit amet',
                'street' => 'Lorem ipsum dolor sit amet',
                'street_number' => 'Lorem ip',
                'complement' => 'Lorem ipsum dolor sit amet',
                'created_at' => 1743809914,
                'updated_at' => 1743809914,
            ],
        ];
        parent::init();
    }
}
