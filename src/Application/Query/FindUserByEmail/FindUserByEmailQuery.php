<?php

declare(strict_types=1);

namespace App\Application\Query\FindUserByEmail;

use App\Application\Query\QueryInterface;

class FindUserByEmailQuery implements QueryInterface
{
    public function __construct(
        public readonly string $email
    ) {
    }
}
