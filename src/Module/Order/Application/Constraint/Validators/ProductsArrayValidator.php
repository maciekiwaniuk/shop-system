<?php

declare(strict_types=1);

namespace App\Module\Order\Application\Constraint\Validators;

use App\Module\Order\Application\Constraint\ProductsArray;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProductsArrayValidator extends ConstraintValidator
{
    /**
     * @param ProductsArray $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $fields = ['id', 'quantity', 'pricePerPiece'];

        $valid = is_array($value) && $this->arrayHasKeys(array: $value, keys: $fields);

        if (!$valid) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    /**
     * @param array<string> $array
     * @param array<string> $keys
     */
    private function arrayHasKeys(array $array, array $keys): bool
    {
        return !array_diff_key(array_flip($keys), $array);
    }
}
