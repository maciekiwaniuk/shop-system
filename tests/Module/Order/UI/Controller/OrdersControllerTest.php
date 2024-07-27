<?php

declare(strict_types=1);

namespace App\Tests\Module\Order\UI\Controller;

use App\Module\Order\Domain\Entity\Order;
use App\Module\Order\Domain\Enum\OrderStatus;
use App\Module\Order\Domain\Repository\OrderRepositoryInterface;
use App\Module\Order\Infrastructure\Doctrine\Generator\OrderGenerator;
use App\Module\Product\Domain\Entity\Product;
use App\Module\Product\Domain\Repository\ProductRepositoryInterface;
use App\Module\Product\Infrastructure\Doctrine\Generator\ProductGenerator;
use App\Module\User\Domain\Repository\UserRepositoryInterface;
use App\Module\User\Infrastructure\Doctrine\Generator\UserGenerator;
use App\Tests\AbstractApplicationTestCase;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class OrdersControllerTest extends AbstractApplicationTestCase
{
    protected string $url = '/api/v1/orders';
    protected readonly UserRepositoryInterface $userRepository;
    protected readonly OrderRepositoryInterface $orderRepository;
    protected readonly ProductRepositoryInterface $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = self::getContainer()->get(UserRepositoryInterface::class);
        $this->orderRepository = self::getContainer()->get(OrderRepositoryInterface::class);
        $this->productRepository = self::getContainer()->get(ProductRepositoryInterface::class);
    }

    public function testGetPaginatedWithoutPassedCursorAsAdmin(): void
    {
        $orders = $this->orderRepository->getPaginatedByUuid(limit: 10);

        $client = $this->getAdminClient();
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
        $orders = $this->orderRepository->getPaginatedByUuid(limit: 10);
        $firstOrder = $orders[0];

        $client = $this->getAdminClient();
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
        $user = (new UserGenerator(self::getContainer()->get(UserPasswordHasherInterface::class)))
            ->generateWithUnhashedPassword(email: 'exampleOrder@email.com');
        $product = (new ProductGenerator())->generate();
        $order = (new OrderGenerator())->generate(
            user: $user,
            products: new ArrayCollection([$product]),
        );

        $client = $this->getUserClient($user);
        $this->productRepository->save($product, true);
        $this->orderRepository->save($order, true);

        $client->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/show/' . $order->getId(),
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

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

    public function testShowAsAdmin(): void
    {
        /** @var Order $order */
        $order = $this->orderRepository->getPaginatedByUuid(limit: 10)[0];

        $client = $this->getAdminClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/show/' . $order->getId(),
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

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

    public function testCreateAsUser(): void
    {
        /** @var Product $product */
        $product = $this->productRepository->getPaginatedById()[0];
        $ordersCountBeforeAction = count($this->orderRepository->getPaginatedByUuid());

        $client = $this->getAdminClient();
        $client->request(
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
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertCount(
            $ordersCountBeforeAction + 1,
            $this->orderRepository->getPaginatedByUuid(),
        );
    }

    public function testChangeStatusAsAdmin(): void
    {
        $user = (new UserGenerator(self::getContainer()->get(UserPasswordHasherInterface::class)))
            ->generate(email: 'exampleOrder@email.com');
        $product = (new ProductGenerator())->generate();
        $order = (new OrderGenerator())->generate(
            user: $user,
            products: new ArrayCollection([$product]),
        );
        $statusBeforeAction = $order->getCurrentStatus();

        $client = $this->getAdminClient();
        $this->productRepository->save($product, true);
        $this->orderRepository->save($order, true);

        $client->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/change-status/' . $order->getId(),
            content: json_encode([
                'status' => OrderStatus::IN_DELIVERY->value,
            ]),
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $orderAfterAction = $this->orderRepository->findByUuid($order->getId());

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertNotEquals($statusBeforeAction, $orderAfterAction->getCurrentStatus());
        $this->assertEquals(OrderStatus::IN_DELIVERY->value, $orderAfterAction->getCurrentStatus());
    }
}
