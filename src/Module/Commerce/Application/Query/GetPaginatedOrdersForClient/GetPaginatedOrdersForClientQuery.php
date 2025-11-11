<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\GetPaginatedOrdersForClient;

use App\Common\Application\Query\QueryInterface;

readonly class GetPaginatedOrdersForClientQuery implements QueryInterface
{
    public function __construct(
        public ?string $cursor,
        public ?int $limit,
    ) {
    }
}
