<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\DTO\Communication;

use App\Module\Auth\Domain\Entity\User;

readonly class UserRegisteredDTO
{
    public function __construct(
        public string $id,
        public string $email,
        public string $name,
        public string $surname,
    ) {
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getEmail(),
            $user->getName(),
            $user->getSurname(),
        );
    }
}
