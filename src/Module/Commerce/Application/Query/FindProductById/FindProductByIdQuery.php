<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\FindProductById;

use App\Common\Application\Query\QueryInterface;

class FindProductByIdQuery implements QueryInterface
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}
