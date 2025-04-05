<?php

declare(strict_types=1);

namespace App\Validation;

use Cake\Validation\Validator;

// Validator for visit endpoints
class VisitRequestValidator
{
    // Validator method for creation endpoints related to Visits
    public static function create(): Validator
    {
        $validator = new Validator();

        // Campos de Visits
        $validator
            ->requirePresence('date', 'create', 'A data da visita é obrigatória.')
            ->notEmptyDate('date', 'A data da visita não pode estar vazia.')
            ->add('date', 'validFormat', [
                'rule' => ['date', ['ymd']],
                'message' => 'Formato de data inválido. Use o padrão YYYY-MM-DD.'
            ]);

        $validator
            ->requirePresence('status', 'create', 'O status da visita é obrigatório.')
            ->notEmptyString('status', 'O status da visita não pode estar vazio.');

        $validator
            ->requirePresence('forms', 'create', 'O campo forms é obrigatório.')
            ->integer('forms', 'O campo forms deve ser um número inteiro.')
            ->greaterThanOrEqual('forms', 0, 'O campo forms não pode ser negativo.');

        $validator
            ->requirePresence('products', 'create', 'O campo products é obrigatório.')
            ->integer('products', 'O campo products deve ser um número inteiro.')
            ->greaterThanOrEqual('products', 0, 'O campo products não pode ser negativo.');

        // Campos de endereço (externos à entidade Visits)
        $validator
            ->requirePresence('postal_code', 'create', 'O CEP é obrigatório.')
            ->notEmptyString('postal_code', 'O CEP não pode estar vazio.')
            ->add('postal_code', 'numericOnly', [
                'rule' => ['custom', '/^\d{8}$/'],
                'message' => 'O CEP deve conter exatamente 8 dígitos numéricos.'
            ]);

        $validator->allowEmptyString('street');
        $validator->allowEmptyString('sublocality');
        $validator->allowEmptyString('complement');
        $validator
            ->requirePresence('street_number', 'create')
            ->notEmptyString('street_number', 'O número é obrigatório')
            ->maxLength('street_number', 10, 'O número deve ter no máximo 10 caracteres')
            ->add('street_number', 'validFormat', [
                'rule' => ['custom', '/^[0-9]+$/'],
                'message' => 'O número deve conter apenas dígitos'
            ]);

        return $validator;
    }
}
