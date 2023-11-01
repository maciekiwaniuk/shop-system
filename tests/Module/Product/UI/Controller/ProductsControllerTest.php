<?php

declare(strict_types=1);

namespace Module\Product\UI\Controller;

use App\Shared\Infrastructure\Doctrine\DataFixtures\AppFixtures;
use App\Tests\AbstractApplicationTestCase;
use Symfony\Component\HttpFoundation\Request;

class ProductsControllerTest extends AbstractApplicationTestCase
{
    protected string $url = '/api/v1/products';

    public function testGetAll(): void
    {
        $client = $this->getUserClient();

        $client->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/get-all'
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals(AppFixtures::$productOneName, $responseData['data'][0]['name']);
    }
}
