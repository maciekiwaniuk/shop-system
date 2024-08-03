<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\SetUserAsAdmin;

use App\Module\Auth\Domain\Entity\User;
use App\Common\Application\Command\CommandInterface;

readonly class SetUserAsAdminCommand implements CommandInterface
{
    public function __construct(
        public User $user,
    ) {
    }
}
