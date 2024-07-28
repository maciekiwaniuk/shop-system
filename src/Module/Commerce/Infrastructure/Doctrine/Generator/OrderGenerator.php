<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Doctrine\Generator;

use App\Module\Commerce\Domain\Entity\Order;
use App\Module\Auth\Domain\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

class OrderGenerator
{
    public function generate(
        User $user,
        ArrayCollection $products,
    ): Order {
        $order = new Order(
            user: $user,
        );

        foreach ($products as $product) {
            $order->createAndAddOrderProduct(
                product: $product,
                productQuantity: mt_rand(1, 30),
                productPricePerPiece: 1 + mt_rand() / mt_getrandmax() * (100 - 1),
            );
        }

        return $order;
    }
}
