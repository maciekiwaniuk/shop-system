<?php

declare(strict_types=1);

namespace App\Module\User\Application\Query\FindUserByEmail;

use App\Shared\Application\Query\QueryInterface;

class FindUserByEmailQuery implements QueryInterface
{
    public function __construct(
        public readonly string $email,
    ) {
    }
}
