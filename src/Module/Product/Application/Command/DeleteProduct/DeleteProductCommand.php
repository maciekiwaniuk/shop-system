<?php

declare(strict_types=1);

namespace App\Module\Product\Application\Command\DeleteProduct;

use App\Module\Product\Domain\Entity\Product;
use App\Shared\Application\Command\CommandInterface;

class DeleteProductCommand implements CommandInterface
{
    public function __construct(
        public readonly Product $product
    ) {
    }
}
