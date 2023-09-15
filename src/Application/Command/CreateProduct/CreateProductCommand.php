<?php

declare(strict_types=1);

namespace App\Application\Command\CreateProduct;

use App\Application\Command\CommandInterface;
use App\Application\DTO\Product\CreateProductDTO;

class CreateProductCommand implements CommandInterface
{
    public function __construct(
        public readonly CreateProductDTO $dto
    ) {
    }
}
