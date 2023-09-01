<?php

declare(strict_types=1);

namespace App\Domain\DTO;

use Symfony\Component\Validator\ConstraintViolationList;

abstract class BaseDTO
{
    private ConstraintViolationList $errors;

    public function setErrors(ConstraintViolationList $errors): void
    {
        $this->errors = $errors;
    }

    public function hasErrors(): bool
    {
        return isset($this->errors) && count($this->errors) > 0;
    }

    public function getErrors(): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $errors[$error->getPropertyPath()] = $error->getMessage();
        }

        return $errors;
    }
}
