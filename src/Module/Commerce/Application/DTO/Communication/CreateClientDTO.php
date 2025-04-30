<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\DTO\Communication;

readonly class CreateClientDTO
{
    public function __construct(
        public string $id,
        public string $email,
        public string $name,
        public string $surname,
    ) {
    }
}
