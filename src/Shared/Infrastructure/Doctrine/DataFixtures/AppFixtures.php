<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\DataFixtures;

use App\Module\User\Infrastructure\Doctrine\Generator\UserGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(
        protected readonly UserGenerator $userGenerator
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->userGenerator->generate();

        $manager->persist($user);
        $manager->flush();
    }
}
