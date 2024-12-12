<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\SyncCommand\SetUserAsAdmin;

use App\Module\Auth\Domain\Entity\User;
use App\Common\Application\SyncCommand\SyncCommandInterface;

readonly class SetUserAsAdminCommand implements SyncCommandInterface
{
    public function __construct(
        public User $user,
    ) {
    }
}
