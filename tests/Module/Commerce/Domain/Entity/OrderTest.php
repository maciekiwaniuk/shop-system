<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Domain\Entity;

use App\Module\Commerce\Domain\Entity\Client;
use App\Module\Commerce\Domain\Entity\Order;
use App\Module\Commerce\Domain\Entity\Product;
use App\Module\Commerce\Domain\Enum\OrderStatus;
use App\Tests\AbstractUnitTestCase;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('unit')]
class OrderTest extends AbstractUnitTestCase
{
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->createMock(Client::class);
    }

    #[Test]
    public function it_should_create_an_order_with_valid_initial_values(): void
    {
        $order = new Order($this->client);

        $this->assertNotNull($order->getId());
        $this->assertNotNull($order->getClient());
        $this->assertNotNull($order->getOrdersProducts());
        $this->assertTrue($order->getOrdersProducts()->isEmpty());
        $this->assertNotNull($order->getCreatedAt());
        $this->assertNull($order->getCompletedAt());
    }

    #[Test]
    public function it_should_have_initial_status_waiting_for_payment(): void
    {
        $order = new Order($this->client);

        $this->assertEquals(OrderStatus::WAITING_FOR_PAYMENT->value, $order->getCurrentStatus());
        $this->assertCount(1, $order->getOrdersStatusUpdates());
    }

    #[Test]
    public function it_should_add_products_to_order(): void
    {
        $product = $this->createMock(Product::class);
        $order = new Order($this->client);

        $order->addProduct($product, 2, 10.50);

        $this->assertCount(1, $order->getOrdersProducts());
        $this->assertFalse($order->getOrdersProducts()->isEmpty());
    }

    #[Test]
    public function it_should_add_multiple_products_to_order(): void
    {
        $product1 = $this->createMock(Product::class);
        $product2 = $this->createMock(Product::class);
        $order = new Order($this->client);

        $order->addProduct($product1, 1, 15.00);
        $order->addProduct($product2, 3, 5.25);

        $this->assertCount(2, $order->getOrdersProducts());
    }

    #[Test]
    public function it_should_update_status_and_create_status_update_record(): void
    {
        $order = new Order($this->client);
        $initialStatusCount = $order->getOrdersStatusUpdates()->count();

        $order->updateStatus(OrderStatus::IN_DELIVERY);

        $this->assertEquals(OrderStatus::IN_DELIVERY->value, $order->getCurrentStatus());
        $this->assertCount($initialStatusCount + 1, $order->getOrdersStatusUpdates());
    }

    #[Test]
    public function it_should_set_completed_at_when_status_changes_to_delivered(): void
    {
        $order = new Order($this->client);
        $this->assertNull($order->getCompletedAt());

        $order->updateStatus(OrderStatus::DELIVERED);

        $this->assertEquals(OrderStatus::DELIVERED->value, $order->getCurrentStatus());
        $this->assertNotNull($order->getCompletedAt());
    }

    #[Test]
    public function it_should_not_set_completed_at_for_non_delivered_status(): void
    {
        $order = new Order($this->client);

        $order->updateStatus(OrderStatus::IN_DELIVERY);

        $this->assertEquals(OrderStatus::IN_DELIVERY->value, $order->getCurrentStatus());
        $this->assertNull($order->getCompletedAt());
    }

    #[Test]
    public function it_should_maintain_status_history_in_order(): void
    {
        $order = new Order($this->client);
        $order->updateStatus(OrderStatus::IN_DELIVERY);
        $order->updateStatus(OrderStatus::DELIVERED);

        $this->assertCount(3, $order->getOrdersStatusUpdates());

        $this->assertEquals(OrderStatus::DELIVERED->value, $order->getCurrentStatus());
    }

    #[Test]
    public function it_should_set_completed_at_manually(): void
    {
        $order = new Order($this->client);
        $completedAt = new DateTimeImmutable('2024-01-15 10:30:00');

        $order->setCompletedAt($completedAt);

        $this->assertEquals($completedAt, $order->getCompletedAt());
    }

    #[Test]
    public function it_should_generate_unique_id_for_each_order(): void
    {
        $order1 = new Order($this->client);
        $order2 = new Order($this->client);

        $this->assertNotEquals($order1->getId(), $order2->getId());
        $this->assertNotEmpty($order1->getId());
        $this->assertNotEmpty($order2->getId());
    }
}
