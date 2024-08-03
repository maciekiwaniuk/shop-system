<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\CreateProduct;

use App\Module\Commerce\Application\DTO\CreateProductDTO;
use App\Common\Application\Command\CommandInterface;

class CreateProductCommand implements CommandInterface
{
    public function __construct(
        public readonly CreateProductDTO $dto,
    ) {
    }
}
