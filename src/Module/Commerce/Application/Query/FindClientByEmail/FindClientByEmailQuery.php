<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\FindClientByEmail;

use App\Common\Application\Query\QueryInterface;

readonly class FindClientByEmailQuery implements QueryInterface
{
    public function __construct(
        public string $email,
    ) {
    }
}
