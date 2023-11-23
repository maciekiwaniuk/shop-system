<?php

declare(strict_types=1);

namespace App\Tests\Module\Order\UI\Controller;

use App\Module\Order\Domain\Repository\OrderRepositoryInterface;
use App\Module\Product\Domain\Entity\Product;
use App\Module\Product\Domain\Repository\ProductRepositoryInterface;
use App\Module\Product\Infrastructure\Doctrine\Generator\ProductGenerator;
use App\Tests\AbstractApplicationTestCase;
use Symfony\Component\HttpFoundation\Request;

class OrdersControllerTest extends AbstractApplicationTestCase
{
    protected string $url = '/api/v1/orders';
    protected readonly OrderRepositoryInterface $orderRepository;
    protected readonly ProductRepositoryInterface $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = self::getContainer()->get(OrderRepositoryInterface::class);
        $this->productRepository = self::getContainer()->get(ProductRepositoryInterface::class);
    }

//    public function testGetPaginatedWithoutPassedCursorAsAdmin(): void
//    {
//        $orders = count($this->orderRepository->getPaginatedByUuid(limit: 10));
//
//        $client = $this->getAdminClient();
//        $client->request(
//            method: Request::METHOD_GET,
//            uri: $this->url . '/get-paginated',
//            parameters: [
//                'limit' => 10
//            ]
//        );
//        $responseData = json_decode($client->getResponse()->getContent(), true);
//
//        $this->assertResponseIsSuccessful();
//        $this->assertTrue($responseData['success']);
//        $this->assertEquals(count($orders), count($responseData['data']));
//    }

//    public function testShowAsOwner(): void
//    {
//
//    }
//
//    public function testShowAsAdmin(): void
//    {
//
//    }
//
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
                        'pricePerPiece' => 3.43
                    ]
                ]
            ])
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertCount(
            $ordersCountBeforeAction + 1,
            $this->orderRepository->getPaginatedByUuid()
        );
    }
//
//    public function testChangeStatusAsAdmin(): void
//    {
//
//    }
}
