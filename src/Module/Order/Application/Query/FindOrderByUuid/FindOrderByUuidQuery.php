<?php

declare(strict_types=1);

namespace App\Module\Order\Application\Query\FindOrderByUuid;

use App\Shared\Application\Query\QueryInterface;

class FindOrderByUuidQuery implements QueryInterface
{
    public function __construct(
        public readonly string $email
    ) {
    }
}
