<?php

declare(strict_types=1);

namespace App\Application\Command\DeleteProduct;

use App\Application\Command\CommandInterface;
use App\Domain\Entity\Product;

class DeleteProductCommand implements CommandInterface
{
    public function __construct(
        public readonly Product $product
    ) {
    }
}
