<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\DataFixtures;

use App\Module\Order\Infrastructure\Doctrine\Generator\OrderGenerator;
use App\Module\Product\Infrastructure\Doctrine\Generator\ProductGenerator;
use App\Module\User\Infrastructure\Doctrine\Generator\UserGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        protected readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new UserGenerator($this->passwordHasher))->generate();

        $productGenerator = new ProductGenerator();
        $productApple = $productGenerator->generate(
            name: 'Apple',
            price: 0.99
        );
        $productBall = $productGenerator->generate(
            name: 'Ball',
            price: 30.99
        );

        $orderOne = (new OrderGenerator())->generate(
            user: $user,
            products: new ArrayCollection([
                $productApple, $productBall
            ])
        );
        $orderTwo = (new OrderGenerator())->generate(
            user: $user,
            products: new ArrayCollection([
                $productBall, $productApple
            ])
        );

        $manager->persist($user);
        $manager->persist($productApple);
        $manager->persist($productBall);
        $manager->persist($orderOne);
        $manager->persist($orderTwo);
        $manager->flush();
    }
}
