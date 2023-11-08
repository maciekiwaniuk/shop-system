<?php

declare(strict_types=1);

namespace App\Tests\Module\Order\UI\Controller;

use App\Module\Order\Domain\Repository\OrderRepositoryInterface;
use App\Tests\AbstractApplicationTestCase;

class OrdersControllerTest extends AbstractApplicationTestCase
{
    protected string $url = '/api/v1/orders';
    protected readonly OrderRepositoryInterface $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = self::getContainer()->get(OrderRepositoryInterface::class);
    }

//    public function testGetAllAsAdmin(): void
//    {
//        $orders = $this->orderRepository->getAll();
//
//        $client = $this->getAdminClient();
//        $client->request(
//            method: Request::METHOD_GET,
//            uri: $this->url . '/get-all'
//        );
//        $responseData = json_decode($client->getResponse()->getContent(), true);
//
//        $this->assertResponseIsSuccessful();
//        $this->assertTrue($responseData['success']);
//        $this->assertEquals($orders[0]->getId(), $responseData['data'][0]['id']);
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
//    public function testCreateAsUser(): void
//    {
//
//    }
//
//    public function testChangeStatusAsAdmin(): void
//    {
//
//    }
}
