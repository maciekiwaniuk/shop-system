<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Domain\Entity;

use App\Module\Commerce\Domain\Entity\Client;
use App\Module\Commerce\Domain\Entity\Order;
use App\Tests\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('unit')]
class OrderTest extends AbstractUnitTestCase
{
    #[Test]
    public function it_should_create_an_order_with_valid_initial_values(): void
    {
        $order = new Order($this->createMock(Client::class));

        $this->assertNotNull($order->getId());
        $this->assertNotNull($order->getClient());
        $this->assertNotNull($order->getOrdersProducts());
        $this->assertTrue($order->getOrdersProducts()->isEmpty());
        $this->assertNotNull($order->getCreatedAt());
        $this->assertNull($order->getCompletedAt());
    }
}
