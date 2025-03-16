<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\UpdateProduct;

use App\Common\Application\SyncCommand\SyncCommandInterface;
use App\Module\Commerce\Application\DTO\Validation\UpdateProductDTO;
use App\Module\Commerce\Domain\Entity\Product;

readonly class UpdateProductCommand implements SyncCommandInterface
{
    public function __construct(
        public Product $product,
        public UpdateProductDTO $dto,
    ) {
    }
}
