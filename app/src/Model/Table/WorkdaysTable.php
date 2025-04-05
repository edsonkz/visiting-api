<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Workdays Model
 *
 * @property \App\Model\Table\VisitsTable&\Cake\ORM\Association\HasMany $Visits
 *
 * @method \App\Model\Entity\Workday newEmptyEntity()
 * @method \App\Model\Entity\Workday newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Workday[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Workday get($primaryKey, $options = [])
 * @method \App\Model\Entity\Workday findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Workday patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Workday[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Workday|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Workday saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Workday[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Workday[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Workday[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Workday[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class WorkdaysTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('workdays');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('Visits', [
            'foreignKey' => 'workday_id',
        ]);

        $this->addBehavior('Timestamp', [
            'created' => 'created_at',
            'modified' => 'updated_at',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->date('date')
            ->requirePresence('date', 'create')
            ->notEmptyDate('date');

        $validator
            ->integer('visits')
            ->notEmptyString('visits');

        $validator
            ->integer('completed')
            ->notEmptyString('completed');

        $validator
            ->integer('duration')
            ->notEmptyString('duration');

        $validator
            ->dateTime('created_at')
            ->notEmptyDateTime('created_at');

        $validator
            ->dateTime('updated_at')
            ->allowEmptyDateTime('updated_at');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['date']), ['errorField' => 'date']);

        return $rules;
    }
}
