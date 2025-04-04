<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Interface\Controller;

use App\Module\Commerce\Domain\Repository\ClientRepositoryInterface;
use App\Module\Commerce\Domain\Repository\ProductRepositoryInterface;
use App\Module\Commerce\Domain\Repository\OrderRepositoryInterface;
use App\Module\Commerce\Infrastructure\Doctrine\Generator\ClientGenerator;
use App\Module\Commerce\Infrastructure\Doctrine\Generator\OrderGenerator;
use App\Module\Commerce\Infrastructure\Doctrine\Generator\ProductGenerator;
use App\Tests\Module\Commerce\AbstractApplicationCommerceTestCase;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

class OrdersControllerTest extends AbstractApplicationCommerceTestCase
{
    private string $url = '/api/v1/orders';
    private ClientRepositoryInterface $clientRepository;
    private OrderRepositoryInterface $orderRepository;
    private ProductRepositoryInterface $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepository = self::getContainer()->get(ClientRepositoryInterface::class);
        $this->orderRepository = self::getContainer()->get(OrderRepositoryInterface::class);
        $this->productRepository = self::getContainer()->get(ProductRepositoryInterface::class);
    }

    public function testGetPaginatedWithoutPassedCursorAsAdmin(): void
    {
        $client = new ClientGenerator()->generate(email: 'exampleOrder@email.com');
        $product = new ProductGenerator()->generate();
        new OrderGenerator()->generate(
            client: $client,
            products: new ArrayCollection([$product]),
        );
        $orders = $this->orderRepository->getPaginatedByUuid(limit: 10);

        $client = $this->getAdminBrowser();
        $client->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/get-paginated',
            parameters: [
                'limit' => 10,
            ],
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals(count($orders), count($responseData['data']));
    }

    public function testGetPaginatedWithPassedCursorAsAdmin(): void
    {
        $client = new ClientGenerator()->generate(email: 'exampleOrder@email.com');
        $product = new ProductGenerator()->generate();
        $order = new OrderGenerator()->generate(
            client: $client,
            products: new ArrayCollection([$product]),
        );
        $this->clientRepository->save($client, true);
        $this->productRepository->save($product, true);
        $this->orderRepository->save($order, true);
        $orders = $this->orderRepository->getPaginatedByUuid(limit: 10);
        $firstOrder = $orders[0];

        $client = $this->getAdminBrowser();
        $client->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/get-paginated',
            parameters: [
                'cursor' => $firstOrder->getId(),
                'limit' => 10,
            ],
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals(count($orders) - 1, count($responseData['data']));
    }

    public function testShowAsOwner(): void
    {
        $client = new ClientGenerator()->generate(email: 'exampleOrder@email.com');
        $clientBrowser = $this->getClientBrowser($client);

        $product = new ProductGenerator()->generate();
        $order = new OrderGenerator()->generate(
            client: $client,
            products: new ArrayCollection([$product]),
        );
        $this->productRepository->save($product, true);
        $this->orderRepository->save($order, true);

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

//    public function testShowAsAdmin(): void
//    {
//        $client = new ClientGenerator()->generate(email: 'exampleOrder@email.com');
//        $product = new ProductGenerator()->generate();
//        $order = new OrderGenerator()->generate(
//            client: $client,
//            products: new ArrayCollection([$product]),
//        );
//        $this->clientRepository->save($client, true);
//        $this->productRepository->save($product, true);
//        $this->orderRepository->save($order, true);
//
//        $order = $this->orderRepository->getPaginatedByUuid(limit: 10)[0];
//
//        $client = $this->getAdminBrowser();
//        $client->request(
//            method: Request::METHOD_GET,
//            uri: $this->url . '/show/' . $order->getId(),
//        );
//        $responseData = json_decode($client->getResponse()->getContent(), true);
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
//
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
//        $client = $this->getAdminBrowser();
//        $client->request(
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
//        $responseData = json_decode($client->getResponse()->getContent(), true);
//
//        $this->assertResponseIsSuccessful();
//        $this->assertTrue($responseData['success']);
//        $this->assertCount(
//            $ordersCountBeforeAction + 1,
//            $this->orderRepository->getPaginatedByUuid(),
//        );
//    }
//
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
//        $client = $this->getAdminBrowser();
//        $this->productRepository->save($product, true);
//        $this->orderRepository->save($order, true);
//
//        $client->request(
//            method: Request::METHOD_POST,
//            uri: $this->url . '/change-status/' . $order->getId(),
//            content: json_encode([
//                'status' => OrderStatus::IN_DELIVERY->value,
//            ]),
//        );
//        $responseData = json_decode($client->getResponse()->getContent(), true);
//
//        $orderAfterAction = $this->orderRepository->findByUuid($order->getId());
//
//        $this->assertResponseIsSuccessful();
//        $this->assertTrue($responseData['success']);
//        $this->assertNotEquals($statusBeforeAction, $orderAfterAction->getCurrentStatus());
//        $this->assertEquals(OrderStatus::IN_DELIVERY->value, $orderAfterAction->getCurrentStatus());
//    }
}
