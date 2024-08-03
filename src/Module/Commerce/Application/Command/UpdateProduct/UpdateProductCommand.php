<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\UpdateProduct;

use App\Module\Commerce\Application\DTO\UpdateProductDTO;
use App\Module\Commerce\Domain\Entity\Product;
use App\Common\Application\Command\CommandInterface;

class UpdateProductCommand implements CommandInterface
{
    public function __construct(
        public readonly Product $product,
        public readonly UpdateProductDTO $dto,
    ) {
    }
}
