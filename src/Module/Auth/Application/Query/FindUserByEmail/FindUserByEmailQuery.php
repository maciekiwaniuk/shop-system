<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Query\FindUserByEmail;

use App\Common\Application\Query\QueryInterface;

class FindUserByEmailQuery implements QueryInterface
{
    public function __construct(
        public readonly string $email,
    ) {
    }
}
