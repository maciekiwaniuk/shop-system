<?php
// src/Module/Auth/Infrastructure/Security/TokenedUser.php
declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

final readonly class TokenedUser implements JWTUserInterface
{
    public function __construct(
        private string $id,
        private array $roles,
    ) {}

    public static function createFromPayload($username, array $payload): self
    {
        return new self($username, $payload['roles'] ?? []);
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->id;
    }
}