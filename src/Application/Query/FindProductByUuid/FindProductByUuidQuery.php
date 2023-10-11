<?php

declare(strict_types=1);

namespace App\Application\Query\FindProductByUuid;

use App\Application\Query\QueryInterface;

class FindProductByUuidQuery implements QueryInterface
{
    public function __construct(
        public readonly string $uuid
    ) {
    }
}
