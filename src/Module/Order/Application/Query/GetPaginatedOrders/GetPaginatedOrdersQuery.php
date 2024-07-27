<?php

declare(strict_types=1);

namespace App\Module\Order\Application\Query\GetPaginatedOrders;

use App\Shared\Application\Query\QueryInterface;

class GetPaginatedOrdersQuery implements QueryInterface
{
    public function __construct(
        public readonly ?string $cursor,
        public readonly int $limit,
    ) {
    }
}
