<?php

declare(strict_types=1);

namespace App\Module\Product\Application\Query\FindProductByUuid;

use App\Shared\Application\Query\QueryInterface;

class FindProductByUuidQuery implements QueryInterface
{
    public function __construct(
        public readonly string $uuid
    ) {
    }
}
