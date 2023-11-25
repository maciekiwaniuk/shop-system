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
        $valid = is_array($value);
        if ($valid) {
            foreach ($value as $product) {
                if (!is_array($product) || !$this->arrayHasValidKeys($product)) {
                    $valid = false;
                }
            }
        }

        if (!$valid) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    /**
     * @param array<string> $array
     */
    private function arrayHasValidKeys(array $array): bool
    {
        return !array_diff_key(
            array_flip(['id', 'quantity', 'pricePerPiece']), $array
        );
    }
}
