<?php

declare(strict_types=1);

namespace App\Validation;

use Cake\Validation\Validator;

// Validator for workdays endpoints
class WorkdayRequestValidator
{

    public static function close()
    {
        $validator = new Validator();

        $validator
            ->requirePresence('date')
            ->notEmptyString('date')
            ->date('date', ['ymd'], 'A data deve estar no formato YYYY-MM-DD.');

        return $validator;
    }
}
