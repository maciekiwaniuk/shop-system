<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\CreateUser;

use App\Module\Auth\Application\DTO\CreateUserDTO;
use App\Common\Application\SyncCommand\SyncCommandInterface;

readonly class CreateUserCommand implements SyncCommandInterface
{
    public function __construct(
        public CreateUserDTO $dto,
    ) {
    }
}
