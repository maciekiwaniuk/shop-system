<?php

declare(strict_types=1);

namespace App\Application\Query\FindProductBySlug;

use App\Application\Query\QueryInterface;

class FindProductBySlugQuery implements QueryInterface
{
    public function __construct(
        public readonly string $uuid
    ) {
    }
}
