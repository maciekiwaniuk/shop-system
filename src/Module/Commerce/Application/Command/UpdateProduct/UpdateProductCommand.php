<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\SyncCommand\UpdateProduct;

use App\Module\Commerce\Application\DTO\UpdateProductDTO;
use App\Module\Commerce\Domain\Entity\Product;
use App\Common\Application\SyncCommand\SyncCommandInterface;

readonly class UpdateProductCommand implements SyncCommandInterface
{
    public function __construct(
        public Product $product,
        public UpdateProductDTO $dto,
    ) {
    }
}
