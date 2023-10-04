<?php

declare(strict_types=1);

namespace App\Application\Query\FindOrderByUuid;

use App\Application\Query\QueryInterface;

class FindOrderByUuidQuery implements QueryInterface
{
    public function __construct(
        public readonly string $email
    ) {
    }
}
