<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\GetPaginatedProducts;

use App\Common\Application\Query\QueryInterface;

readonly class GetPaginatedProductsQuery implements QueryInterface
{
    public function __construct(
        public int $offset,
        public int $limit,
    ) {
    }
}
