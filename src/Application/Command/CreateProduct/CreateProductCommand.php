<?php

namespace App\Application\Command\CreateProduct;

use App\Application\Command\CommandInterface;
use App\Domain\DTO\Product\CreateProductDTO;

class CreateProductCommand implements CommandInterface
{
    public function __construct(
        public readonly CreateProductDTO $dto
    ) {
    }
}