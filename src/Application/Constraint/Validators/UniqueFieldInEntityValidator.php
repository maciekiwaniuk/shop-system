<?php

declare(strict_types=1);

namespace App\Application\Constraint\Validators;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueFieldInEntityValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ManagerRegistry $doctrine
    ) {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        $field = $constraint->field;
        $entityClassName = $constraint->entityClassName;

        $foundRecord = $this->doctrine->getRepository($entityClassName)
            ->findOneBy([$field => $value]);

        if (!$foundRecord) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ field }}', ucfirst($field))
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
