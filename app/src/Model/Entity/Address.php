<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Address Entity
 *
 * @property int $id
 * @property string $postal_code
 * @property string $state
 * @property string $city
 * @property string $sublocality
 * @property string $street
 * @property string $street_number
 * @property string $complement
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 *
 * @property \App\Model\Entity\Visit $visits
 */
class Address extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'postal_code' => true,
        'state' => true,
        'city' => true,
        'sublocality' => true,
        'street' => true,
        'street_number' => true,
        'complement' => true,
        'created_at' => true,
        'updated_at' => true,
        'visit' => true,
    ];

    // Get postal_code formatted as CEP
    protected function _getFormattedPostalCode(): string
    {
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $this->postal_code);
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        // Change postal_code with the formatted version
        if (isset($data['postal_code'])) {
            $data['postal_code'] = $this->formatted_postal_code;
        }

        return $data;
    }
}
