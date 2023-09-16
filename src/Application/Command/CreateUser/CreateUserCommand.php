<?php

declare(strict_types=1);

namespace App\Application\Command\CreateUser;

use App\Application\Command\CommandInterface;
use App\Application\DTO\User\CreateUserDTO;

class CreateUserCommand implements CommandInterface
{
    public function __construct(
        public readonly CreateUserDTO $dto
    ) {
    }
}
