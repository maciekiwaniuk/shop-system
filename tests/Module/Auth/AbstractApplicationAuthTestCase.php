<?php

declare(strict_types=1);

namespace App\Tests\Module\Auth;

use App\Module\Auth\Domain\Entity\User;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Tests\AbstractApplicationTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AbstractApplicationAuthTestCase extends AbstractApplicationTestCase
{
    public function insertUser(?User $user = null): User
    {
        if ($user === null) {
            $user = new User(
                email: 'example@email.com',
                password: 'examplePassword',
                name: 'exampleName',
                surname: 'exampleSurname',
            );
        }

        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $user->getPassword()),
        );

        $userRepository = self::getContainer()->get(UserRepositoryInterface::class);
        $userRepository->save($user, true);

        return $user;
    }
}
