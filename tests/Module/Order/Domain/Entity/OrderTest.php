<?php

declare(strict_types=1);

namespace App\Tests\Module\Order\Domain\Entity;

use App\Module\Order\Domain\Entity\Order;
use App\Module\User\Domain\Entity\User;
use App\Tests\AbstractUnitTestCase;

class OrderTest extends AbstractUnitTestCase
{
    public function testCreate(): void
    {
        $order = new Order($this->createMock(User::class));

        $this->assertNotNull($order->getId());
        $this->assertNotNull($order->getUser());
        $this->assertNotNull($order->getOrdersProducts());
        $this->assertFalse($order->getOrdersStatusUpdates()->isEmpty());
        $this->assertNotNull($order->getCreatedAt());
        $this->assertNull($order->getCompletedAt());
    }
}
