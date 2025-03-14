<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Doctrine\Generator;

use App\Module\Commerce\Domain\Entity\Client;
use App\Module\Commerce\Domain\Entity\Order;
use Doctrine\Common\Collections\ArrayCollection;

class OrderGenerator
{
    public function generate(
        Client $client,
        ArrayCollection $products,
    ): Order {
        $order = new Order(
            client: $client,
        );

        foreach ($products as $product) {
            $order->addProduct(
                product: $product,
                productQuantity: mt_rand(1, 30),
                productPricePerPiece: 1 + mt_rand() / mt_getrandmax() * (100 - 1),
            );
        }

        return $order;
    }
}
