<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\FindProductBySlug;

use App\Common\Application\Query\QueryInterface;

readonly class FindProductBySlugQuery implements QueryInterface
{
    public function __construct(
        public string $slug,
    ) {
    }
}
