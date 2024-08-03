<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\GetPaginatedOrders;

use App\Common\Application\Query\QueryInterface;

class GetPaginatedOrdersQuery implements QueryInterface
{
    public function __construct(
        public readonly ?string $cursor,
        public readonly int $limit,
    ) {
    }
}
