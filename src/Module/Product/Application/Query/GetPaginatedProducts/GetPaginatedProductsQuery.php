<?php

declare(strict_types=1);

namespace App\Module\Product\Application\Query\GetPaginatedProducts;

use App\Shared\Application\Query\QueryInterface;

class GetPaginatedProductsQuery implements QueryInterface
{
    public function __construct(
        public readonly int $offset,
        public readonly int $limit,
    ) {
    }
}
