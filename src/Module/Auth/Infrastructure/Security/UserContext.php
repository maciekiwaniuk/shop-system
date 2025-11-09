<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Security;

use App\Common\Application\Security\UserContextInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class UserContext implements UserContextInterface
{
    private ?UserInterface $user;

    public function __construct(Security $security)
    {
        $user = $security->getUser();
        $this->user = $user instanceof UserInterface ? $user : null;
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->user?->getRoles() ?? [], true);
    }

    public function getUserIdentifier(): string
    {
        return $this->user?->getUserIdentifier() ?? '';
    }

    public function getUser(): ?object
    {
        return $this->user;
    }
}
