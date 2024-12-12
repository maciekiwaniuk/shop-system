<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\SyncCommand\DeleteProduct;

use App\Module\Commerce\Domain\Entity\Product;
use App\Common\Application\SyncCommand\SyncCommandInterface;

readonly class DeleteProductCommand implements SyncCommandInterface
{
    public function __construct(
        public Product $product,
    ) {
    }
}
