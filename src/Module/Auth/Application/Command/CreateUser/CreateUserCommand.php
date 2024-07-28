<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\CreateUser;

use App\Module\Auth\Application\DTO\CreateUserDTO;
use App\Shared\Application\Command\CommandInterface;

class CreateUserCommand implements CommandInterface
{
    public function __construct(
        public readonly CreateUserDTO $dto,
    ) {
    }
}
