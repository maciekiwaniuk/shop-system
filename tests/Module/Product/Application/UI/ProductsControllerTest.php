<?php

declare(strict_types=1);

namespace App\Tests\Module\Product\Application\UI;

use App\Tests\AbstractApplicationTestCase;

class ProductsControllerTest extends AbstractApplicationTestCase
{
    public function testGetAll(): void
    {
        $guestClient = $this->getGuestClient();

        $guestClient->request('GET', '/api/v1/products/get-all');

        $response = $guestClient->getResponse();
        $data = json_decode($response->getContent(), true);

        $this->assertTrue(true);
    }
//
//    public function testNew(): void
//    {
//
//    }
//
//    public function testShow(): void
//    {
//
//    }
//
//    public function testUpdate(): void
//    {
//
//    }
//
//    public function testDelete(): void
//    {
//
//    }
}
