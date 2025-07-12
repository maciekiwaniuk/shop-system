<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\FindOrderByUuid;

use App\Common\Application\Query\QueryInterface;

readonly class FindOrderByUuidQuery implements QueryInterface
{
    public function __construct(
        public string $uuid,
    ) {
    }
}
