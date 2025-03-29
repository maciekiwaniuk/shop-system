<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Doctrine\Generator;

use App\Module\Commerce\Domain\Entity\Client;
use Symfony\Component\Uid\Uuid;

readonly class ClientGenerator
{
    public function generate(
        string $email = 'test1234@email.com',
        string $name = 'exampleName',
        string $surname = 'exampleSurname',
    ): Client {
        return new Client(
            id: (string) Uuid::v1(),
            email: $email,
            name: $name,
            surname: $surname,
        );
    }
}