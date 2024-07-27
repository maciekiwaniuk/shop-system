<?php

declare(strict_types=1);

namespace App\Module\User\Application\Command\SetUserAsAdmin;

use App\Module\User\Domain\Entity\User;
use App\Shared\Application\Command\CommandInterface;

class SetUserAsAdminCommand implements CommandInterface
{
    public function __construct(
        public readonly User $user,
    ) {
    }
}
