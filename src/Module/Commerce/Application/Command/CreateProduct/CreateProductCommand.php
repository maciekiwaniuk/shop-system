<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\SyncCommand\CreateProduct;

use App\Module\Commerce\Application\DTO\CreateProductDTO;
use App\Common\Application\SyncCommand\SyncCommandInterface;

readonly class CreateProductCommand implements SyncCommandInterface
{
    public function __construct(
        public CreateProductDTO $dto,
    ) {
    }
}
