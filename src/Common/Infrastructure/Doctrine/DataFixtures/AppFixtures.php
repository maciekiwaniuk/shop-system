<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Doctrine\DataFixtures;

use App\Module\Commerce\Infrastructure\Doctrine\Generator\OrderGenerator;
use App\Module\Commerce\Infrastructure\Doctrine\Generator\ProductGenerator;
use App\Module\Auth\Infrastructure\Doctrine\Generator\UserGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // TODO:
//        $user = (new UserGenerator($this->passwordHasher))->generate();
//
//        $productGenerator = new ProductGenerator();
//        $productApple = $productGenerator->generate(
//            name: 'Apple',
//            price: 0.99,
//        );
//        $productBall = $productGenerator->generate(
//            name: 'Ball',
//            price: 30.99,
//        );
//
//        $orderOne = new OrderGenerator()->generate(
//            user: $user,
//            products: new ArrayCollection([
//                $productApple, $productBall,
//            ]),
//        );
//        $orderTwo = new OrderGenerator()->generate(
//            user: $user,
//            products: new ArrayCollection([
//                $productBall, $productApple,
//            ]),
//        );
//
//        $manager->persist($user);
//        $manager->persist($productApple);
//        $manager->persist($productBall);
//        $manager->persist($orderOne);
//        $manager->persist($orderTwo);
//        $manager->flush();
    }
}
