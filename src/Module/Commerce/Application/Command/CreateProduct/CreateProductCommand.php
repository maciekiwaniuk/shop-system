<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\CreateProduct;

use App\Common\Application\SyncCommand\SyncCommandInterface;
use App\Module\Commerce\Application\DTO\Validation\CreateProductDTO;

readonly class CreateProductCommand implements SyncCommandInterface
{
    public function __construct(
        public CreateProductDTO $dto,
    ) {
    }
}
