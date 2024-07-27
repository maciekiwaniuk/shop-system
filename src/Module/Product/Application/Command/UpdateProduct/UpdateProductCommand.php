<?php

declare(strict_types=1);

namespace App\Module\Product\Application\Command\UpdateProduct;

use App\Module\Product\Application\DTO\UpdateProductDTO;
use App\Module\Product\Domain\Entity\Product;
use App\Shared\Application\Command\CommandInterface;

class UpdateProductCommand implements CommandInterface
{
    public function __construct(
        public readonly Product $product,
        public readonly UpdateProductDTO $dto,
    ) {
    }
}
