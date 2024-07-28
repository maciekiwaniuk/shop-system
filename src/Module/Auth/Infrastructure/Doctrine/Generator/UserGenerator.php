<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Generator;

use App\Module\Auth\Domain\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserGenerator
{
    public function __construct(
        protected readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function generate(
        string $email = 'test1234@email.com',
        string $password = 'test1234',
        string $name = 'exampleName',
        string $surname = 'exampleSurname',
    ): User {
        $user = new User(
            email: $email,
            password: $password,
            name: $name,
            surname: $surname,
        );
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $user->getPassword()),
        );
        return $user;
    }

    public function generateWithUnhashedPassword(
        string $email = 'test1234@email.com',
        string $password = 'test1234',
        string $name = 'exampleName',
        string $surname = 'exampleSurname',
    ): User {
        return new User(
            email: $email,
            password: $password,
            name: $name,
            surname: $surname,
        );
    }
}
