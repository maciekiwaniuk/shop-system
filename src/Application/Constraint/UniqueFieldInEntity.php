<?php

declare(strict_types=1);

namespace App\Application\Constraint;

use App\Application\Constraint\Validators\UniqueFieldInEntityValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueFieldInEntity extends Constraint
{
    public string $message = '{{ field }} {{ value }} is already in use.';
    public string $field;
    public string $entityClassName;

    public function __construct(
        string $field,
        string $entityClassName,
        mixed $options = null,
        array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options, $groups, $payload);

        $this->field = $field;
        $this->entityClassName = $entityClassName;
    }

    public function getTargets(): array|string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return UniqueFieldInEntityValidator::class;
    }
}