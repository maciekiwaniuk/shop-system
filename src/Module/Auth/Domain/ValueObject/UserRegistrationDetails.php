<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

readonly class UserRegistrationDetails
{
    public function __construct(
        public string $id,
        public string $email,
        public string $name,
        public string $surname,
    ) {
    }
}
