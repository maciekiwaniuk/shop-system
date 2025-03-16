<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\CreateUser;

use App\Common\Application\SyncCommand\SyncCommandInterface;
use App\Module\Auth\Application\DTO\Validation\CreateUserDTO;

readonly class CreateUserCommand implements SyncCommandInterface
{
    public function __construct(
        public CreateUserDTO $dto,
    ) {
    }
}
