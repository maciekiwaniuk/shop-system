<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\FindClientById;

use App\Common\Application\Query\QueryInterface;

readonly class FindClientByIdQuery implements QueryInterface
{
    public function __construct(
        public string $id,
    ) {
    }
}
