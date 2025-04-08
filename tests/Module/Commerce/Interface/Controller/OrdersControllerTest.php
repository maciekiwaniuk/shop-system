<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Interface\Controller;

use App\Module\Commerce\Domain\Entity\Client;
use App\Module\Commerce\Domain\Enum\OrderStatus;
use App\Module\Commerce\Domain\Repository\OrderRepositoryInterface;
use App\Tests\Module\Commerce\AbstractApplicationCommerceTestCase;
use DateTimeImmutable;
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

//    /** @test */
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

//    /** @test */
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

    /** @test */
    public function can_show_order_as_owner(): void
    {
        $clientAuthenticatedData = $this->getClientBrowser();
        var_dump('test1234');
        $order = $this->insertOrder(client: $clientAuthenticatedData['client']);
        $clientBrowser = $clientAuthenticatedData['clientBrowser'];
        $clientBrowser->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/show/' . $order->getId(),
        );
        $responseData = json_decode($clientBrowser->getResponse()->getContent(), true);
        var_dump('response data');
        var_dump($clientBrowser->getResponse()->getStatusCode());
        var_dump($responseData);

//        $this->assertResponseIsSuccessful();
//        $this->assertTrue($responseData['success']);
//        $this->assertEquals(
//            $order->getCreatedAt()->setTime(
//                (int) $order->getCreatedAt()->format('H'),
//                (int) $order->getCreatedAt()->format('i'),
//                (int) $order->getCreatedAt()->format('s'),
//            ),
//            new DateTimeImmutable($responseData['data']['createdAt']),
//        );
    }

//    /** @test */
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

//    /** @test */
//    public function can_create_order_as_user(): void
//    {
//        $client = new ClientGenerator()->generate(email: 'exampleOrder@email.com');
//        $product = new ProductGenerator()->generate();
//        new OrderGenerator()->generate(
//            client: $client,
//            products: new ArrayCollection([$product]),
//        );
//        /** @var Product $product */
//        $product = $this->productRepository->getPaginatedById()[0];
//        $ordersCountBeforeAction = count($this->orderRepository->getPaginatedByUuid());
//
//        $adminBrowser = $this->getAdminBrowser();
//        $adminBrowser->request(
//            method: Request::METHOD_POST,
//            uri: $this->url . '/create',
//            content: json_encode([
//                'products' => [
//                    [
//                        'id' => $product->getId(),
//                        'quantity' => 4,
//                        'pricePerPiece' => 3.43,
//                    ],
//                ],
//            ]),
//        );
//        $responseData = json_decode($adminBrowser->getResponse()->getContent(), true);
//
//        $this->assertResponseIsSuccessful();
//        $this->assertTrue($responseData['success']);
//        $this->assertCount(
//            $ordersCountBeforeAction + 1,
//            $this->orderRepository->getPaginatedByUuid(),
//        );
//    }

//    /** @test */
//    public function can_change_order_status_as_admin(): void
//    {
//        $client = new ClientGenerator()->generate(email: 'exampleOrder@email.com');
//        $product = new ProductGenerator()->generate();
//        $order = new OrderGenerator()->generate(
//            client: $client,
//            products: new ArrayCollection([$product]),
//        );
//        $statusBeforeAction = $order->getCurrentStatus();
//
//        $adminBrowser = $this->getAdminBrowser();
//        $this->productRepository->save($product, true);
//        $this->orderRepository->save($order, true);
//
//        $adminBrowser->request(
//            method: Request::METHOD_POST,
//            uri: $this->url . '/change-status/' . $order->getId(),
//            content: json_encode([
//                'status' => OrderStatus::IN_DELIVERY->value,
//            ]),
//        );
//        $responseData = json_decode($adminBrowser->getResponse()->getContent(), true);
//
//        $orderAfterAction = $this->orderRepository->findByUuid($order->getId());
//
//        $this->assertResponseIsSuccessful();
//        $this->assertTrue($responseData['success']);
//        $this->assertNotEquals($statusBeforeAction, $orderAfterAction->getCurrentStatus());
//        $this->assertEquals(OrderStatus::IN_DELIVERY->value, $orderAfterAction->getCurrentStatus());
//    }
}
