<?php

declare(strict_types=1);

namespace App\Module\Order\Application\Constraint;

use App\Module\Order\Application\Constraint\Validators\ProductsArrayValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ProductsArray extends Constraint
{
    public string $message = 'Invalid array of products.';

    public function __construct(
        mixed $options = null,
        array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function getTargets(): array|string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return ProductsArrayValidator::class;
    }
}
