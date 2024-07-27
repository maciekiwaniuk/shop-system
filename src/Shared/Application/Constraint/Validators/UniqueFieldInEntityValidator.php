<?php

declare(strict_types=1);

namespace App\Shared\Application\Constraint\Validators;

use App\Shared\Application\Constraint\UniqueFieldInEntity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueFieldInEntityValidator extends ConstraintValidator
{
    public function __construct(
        protected readonly ManagerRegistry $doctrine,
    ) {
    }

    /**
     * @param UniqueFieldInEntity $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $foundRecord = $this->doctrine
            ->getRepository($constraint->entityClassName)
            ->findOneBy([$constraint->field => $value]);

        if (!$foundRecord) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ field }}', ucfirst($constraint->field))
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
