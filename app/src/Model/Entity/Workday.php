<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Workday Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenDate $date
 * @property int $completed
 * @property int $duration
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime|null $updated_at
 *
 * @property \App\Model\Entity\Visit[] $visits
 */
class Workday extends Entity
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
        'visits' => true,
        'completed' => true,
        'duration' => true,
        'created_at' => true,
        'updated_at' => true,
    ];
}
