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

    public function testGetPaginatedWithoutPassedCursorAsAdmin(): void
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

    public function testGetPaginatedWithPassedCursorAsAdmin(): void
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

//    public function testShowAsOwner(): void
//    {
//        $client = new Client(
//            id: Uuid::v4()->toString(),
//            email: 'example123@email.com',
//            name: 'John',
//            surname: 'Paul'
//        );
//        $clientBrowser = $this->getClientBrowser($client);
//        $order = $this->insertOrder(client: $client);
//        $clientBrowser->request(
//            method: Request::METHOD_GET,
//            uri: $this->url . '/show/' . $order->getId(),
//        );
//        $responseData = json_decode($clientBrowser->getResponse()->getContent(), true);
//
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
//    }

    public function testShowAsAdmin(): void
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

//    public function testCreateAsUser(): void
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

//    public function testChangeStatusAsAdmin(): void
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
