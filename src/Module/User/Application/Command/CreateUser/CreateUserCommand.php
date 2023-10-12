<?php

declare(strict_types=1);

namespace App\Module\User\Application\Command\CreateUser;

use App\Module\User\Application\DTO\CreateUserDTO;
use App\Shared\Application\Command\CommandInterface;

class CreateUserCommand implements CommandInterface
{
    public function __construct(
        public readonly CreateUserDTO $dto
    ) {
    }
}
