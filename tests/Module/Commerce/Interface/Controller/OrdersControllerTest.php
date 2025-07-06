<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Interface\Controller;

use App\Module\Commerce\Domain\Entity\Client;
use App\Module\Commerce\Domain\Enum\OrderStatus;
use App\Module\Commerce\Domain\Repository\ClientRepositoryInterface;
use App\Module\Commerce\Domain\Repository\OrderRepositoryInterface;
use App\Tests\Module\Commerce\AbstractApplicationCommerceTestCase;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class OrdersControllerTest extends AbstractApplicationCommerceTestCase
{
    private string $url = '/api/v1/orders';
    private OrderRepositoryInterface $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = self::getContainer()->get(OrderRepositoryInterface::class);
    }

    #[Test]
    public function can_get_paginated_data_without_passed_cursor_as_admin(): void
    {
        $this->insertOrder();
        $orders = $this->orderRepository->getPaginatedByUuid(limit: 10);
        $adminBrowser = $this->getAdminBrowser();

        $adminBrowser->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/get-paginated',
            parameters: [
                'limit' => 10,
            ],
        );

        $responseData = json_decode($adminBrowser->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals(count($orders), count($responseData['data']));
    }

    #[Test]
    public function can_get_paginated_data_with_passed_cursor_as_admin(): void
    {
        $this->insertOrder();
        $orders = $this->orderRepository->getPaginatedByUuid(limit: 10);
        $firstOrder = $orders[0];
        $adminBrowser = $this->getAdminBrowser();

        $adminBrowser->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/get-paginated',
            parameters: [
                'cursor' => $firstOrder->getId(),
                'limit' => 10,
            ],
        );

        $responseData = json_decode($adminBrowser->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals(count($orders) - 1, count($responseData['data']));
    }

    #[Test]
    public function can_show_order_as_owner(): void
    {
        $client = new Client(
            id: (string) Uuid::v4(),
            email: 'test1234@wp.pl',
            name: 'test',
            surname: 'test',
        );
        $clientBrowser = $this->getClientBrowser(client: $client);
        $clientRepository = self::getContainer()->get(ClientRepositoryInterface::class);
        $clientEntity = $clientRepository->findClientByEmail($client->getEmail());
        $order = $this->insertOrder(client: $clientEntity);

        $clientBrowser->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/show/' . $order->getId(),
        );

        $responseData = json_decode($clientBrowser->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals(
            $order->getCreatedAt()->setTime(
                (int) $order->getCreatedAt()->format('H'),
                (int) $order->getCreatedAt()->format('i'),
                (int) $order->getCreatedAt()->format('s'),
            ),
            new DateTimeImmutable($responseData['data']['createdAt']),
        );
    }

    #[Test]
    public function can_show_order_someones_else_as_admin(): void
    {
        $this->insertOrder();
        $order = $this->orderRepository->getPaginatedByUuid(limit: 10)[0];
        $adminBrowser = $this->getAdminBrowser();

        $adminBrowser->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/show/' . $order->getId(),
        );

        $responseData = json_decode($adminBrowser->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals(
            $order->getCreatedAt()->setTime(
                (int) $order->getCreatedAt()->format('H'),
                (int) $order->getCreatedAt()->format('i'),
                (int) $order->getCreatedAt()->format('s'),
            ),
            new DateTimeImmutable($responseData['data']['createdAt']),
        );
    }

    #[Test]
    public function can_create_order_as_client(): void
    {
        $ordersCountBeforeAction = count($this->orderRepository->getPaginatedByUuid(limit: 10));
        $product = $this->insertProduct();
        $clientBrowser = $this->getClientBrowser();

        $clientBrowser->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/create',
            content: json_encode([
                'products' => [
                    [
                        'id' => $product->getId(),
                        'quantity' => 4,
                        'pricePerPiece' => 3.43,
                    ],
                ],
            ]),
        );

        $responseData = json_decode($clientBrowser->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertCount(
            $ordersCountBeforeAction + 1,
            $this->orderRepository->getPaginatedByUuid(),
        );
    }

    #[Test]
    public function can_change_order_status_as_admin(): void
    {
        $order = $this->insertOrder();
        $statusBeforeAction = $order->getCurrentStatus();
        $adminBrowser = $this->getAdminBrowser();

        $adminBrowser->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/change-status/' . $order->getId(),
            content: json_encode([
                'status' => OrderStatus::IN_DELIVERY->value,
            ]),
        );

        $responseData = json_decode($adminBrowser->getResponse()->getContent(), true);
        $orderAfterAction = $this->orderRepository->findByUuid($order->getId());
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertNotEquals($statusBeforeAction, $orderAfterAction->getCurrentStatus());
        $this->assertEquals(OrderStatus::IN_DELIVERY->value, $orderAfterAction->getCurrentStatus());
    }
}
