<?php

declare(strict_types=1);

namespace App\Module\User\Domain\Repository;

use App\Module\User\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user, bool $flush = false): void;

    public function findUserByEmail(string $email): ?User;
}
