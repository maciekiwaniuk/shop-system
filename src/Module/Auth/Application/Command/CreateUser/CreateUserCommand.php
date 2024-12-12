<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\SyncCommand\CreateUser;

use App\Module\Auth\Application\DTO\CreateUserDTO;
use App\Common\Application\SyncCommand\SyncCommandInterface;

readonly class CreateUserCommand implements SyncCommandInterface
{
    public function __construct(
        public CreateUserDTO $dto,
    ) {
    }
}
