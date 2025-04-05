<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Visit Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenDate $date
 * @property string $status
 * @property bool $completed
 * @property int $forms
 * @property int $products
 * @property int $duration
 * @property int $address_id
 * @property int $workday_id
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 *
 * @property \App\Model\Entity\Address $address
 * @property \App\Model\Entity\Workday $workday
 */
class Visit extends Entity
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
        'date' => true,
        'status' => true,
        'completed' => true,
        'forms' => true,
        'products' => true,
        'duration' => true,
        'address_id' => true,
        'workday_id' => true,
        'created_at' => true,
        'updated_at' => true,
        'address' => true,
        'workday' => true,
    ];
}
