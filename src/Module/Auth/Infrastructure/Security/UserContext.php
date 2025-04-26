<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Security;

use App\Common\Application\Security\UserContextInterface;
use App\Module\Auth\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class UserContext implements UserContextInterface
{
    private ?User $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $token = $tokenStorage->getToken();
        $this->user = $token?->getUser() instanceof User
            ? $token->getUser()
            : null;
    }

    public function isAdmin(): bool
    {
        return $this->user?->isAdmin() ?? false;
    }

    public function getUserIdentifier(): string
    {
        return $this->user?->getUserIdentifier() ?? '';
    }
}
