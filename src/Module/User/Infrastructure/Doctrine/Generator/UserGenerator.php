<?php

declare(strict_types=1);

namespace App\Module\User\Infrastructure\Doctrine\Generator;

use App\Module\User\Domain\Entity\User;

class UserGenerator
{
    public function generate(
        string $email = 'test1234@email.com',
        string $password = 'test1234',
        string $name = 'exampleName',
        string $surname = 'exampleSurname'
    ): User {
        return new User(
            email: $email,
            password: $password,
            name: $name,
            surname: $surname
        );
    }
}
