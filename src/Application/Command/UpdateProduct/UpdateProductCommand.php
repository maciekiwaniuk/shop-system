<?php

declare(strict_types=1);

namespace App\Application\Command\UpdateProduct;

use App\Application\Command\CommandInterface;
use App\Application\DTO\Product\UpdateProductDTO;
use App\Domain\Entity\Product;

class UpdateProductCommand implements CommandInterface
{
    public function __construct(
        public readonly Product $product,
        public readonly UpdateProductDTO $dto
    ) {
    }
}
