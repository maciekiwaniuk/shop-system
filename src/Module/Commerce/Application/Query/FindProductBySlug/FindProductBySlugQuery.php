<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\FindProductBySlug;

use App\Common\Application\Query\QueryInterface;

class FindProductBySlugQuery implements QueryInterface
{
    public function __construct(
        public readonly string $slug,
    ) {
    }
}
