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
    public function __construct(
        protected readonly UserGenerator $userGenerator,
        protected readonly ProductGenerator $productGenerator,
        protected readonly OrderGenerator $orderGenerator
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->userGenerator->generate();

        $productApple = $this->productGenerator->generate(
            name: 'Apple',
            price: 0.99
        );
        $productBall = $this->productGenerator->generate(
            name: 'Ball',
            price: 30.99
        );

        $order = $this->orderGenerator->generate(
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
