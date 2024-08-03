<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\GetPaginatedOrders;

use App\Common\Application\Query\QueryInterface;

readonly class GetPaginatedOrdersQuery implements QueryInterface
{
    public function __construct(
        public ?string $cursor,
        public int $limit,
    ) {
    }
}
