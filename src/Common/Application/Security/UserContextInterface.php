<?php

declare(strict_types=1);

namespace App\Common\Application\Security;

interface UserContextInterface
{
    public function isAdmin(): bool;

    public function getUserIdentifier(): string;

    public function getUser(): ?object;
}
