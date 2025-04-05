<?php
declare(strict_types=1);

namespace App\Error\Exception;

use Cake\Core\Exception\CakeException;

class ApiValidationException extends CakeException
{
    protected array $errors;

    public function __construct(string $message, array $errors = [], int $code = 422)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
