<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\DataFixtures;

use App\Module\Order\Infrastructure\Doctrine\Generator\OrderGenerator;
use App\Module\Product\Infrastructure\Doctrine\Generator\ProductGenerator;
use App\Module\User\Infrastructure\Doctrine\Generator\UserGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public static string $userEmail = 'fixture@email.com';
    public static string $productOneName = 'Apple';
    public static string $productTwoName = 'Ball';

    public function load(ObjectManager $manager): void
    {
        $userGenerator = new UserGenerator();
        $productGenerator = new ProductGenerator();
        $orderGenerator = new OrderGenerator();
        
        $user = $userGenerator->generate(
            email: self::$userEmail
        );

        $productApple = $productGenerator->generate(
            name: self::$productOneName,
            price: 0.99
        );
        $productBall = $productGenerator->generate(
            name: self::$productTwoName,
            price: 30.99
        );

        $order = $orderGenerator->generate(
            user: $user,
            products: new ArrayCollection([
                $productApple, $productBall
            ])
        );

        $manager->persist($user);
        $manager->persist($productApple);
        $manager->persist($productBall);
        $manager->persist($order);
        $manager->flush();
    }
}
