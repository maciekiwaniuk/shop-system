<?php

declare(strict_types=1);

namespace App\Module\Product\Infrastructure\Doctrine\Generator;

use App\Module\Product\Domain\Entity\Product;

class ProductGenerator
{
    public function generate(
        string $name = 'exampleName',
        float $price = 41.33,
    ): Product {
        return new Product(
            name: $name,
            price: $price,
        );
    }
}
