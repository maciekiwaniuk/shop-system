<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\GetPaginatedProducts;

use App\Common\Application\Query\QueryInterface;

class GetPaginatedProductsQuery implements QueryInterface
{
    public function __construct(
        public readonly int $offset,
        public readonly int $limit,
    ) {
    }
}
