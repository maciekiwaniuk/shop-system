<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\DTO\Communication;

use App\Module\Auth\Domain\Entity\User;

readonly class SendWelcomeEmailDTO
{
    public function __construct(
        public string $email,
        public string $name,
        public string $surname,
    ) {
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            $user->getEmail(),
            $user->getName(),
            $user->getSurname(),
        );
    }
}
