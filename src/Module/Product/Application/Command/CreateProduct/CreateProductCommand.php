<?php

declare(strict_types=1);

namespace App\Module\Product\Application\Command\CreateProduct;

use App\Module\Product\Application\DTO\CreateProductDTO;
use App\Shared\Application\Command\CommandInterface;

class CreateProductCommand implements CommandInterface
{
    public function __construct(
        public readonly CreateProductDTO $dto
    ) {
    }
}
