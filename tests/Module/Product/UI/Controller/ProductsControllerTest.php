<?php

declare(strict_types=1);

namespace Module\Product\UI\Controller;

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

        $this->assertResponseIsSuccessful();
    }
}
