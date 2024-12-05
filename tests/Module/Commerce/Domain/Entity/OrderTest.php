<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Domain\Entity;

use App\Module\Commerce\Domain\Entity\Order;
use App\Module\Auth\Domain\Entity\User;
use App\Tests\AbstractUnitTestCase;

class OrderTest extends AbstractUnitTestCase
{
    public function testCreate(): void
    {
        $order = new Order($this->createMock(User::class));

        $this->assertNotNull($order->getId());
        $this->assertNotNull($order->getUser());
        $this->assertNotNull($order->ordersProducts);
        $this->assertFalse($order->ordersStatusUpdates->isEmpty());
        $this->assertNotNull($order->getCreatedAt());
        $this->assertNull($order->getCompletedAt());
    }
}
