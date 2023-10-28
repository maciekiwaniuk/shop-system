<?php

declare(strict_types=1);

namespace Module\Product\UI\Controller;

use App\Tests\AbstractApplicationTestCase;

class ProductsControllerTest extends AbstractApplicationTestCase
{
    protected string $url = '/api/v1/products';

    public function testGetAll(): void
    {
        $client = $this->getUserClient();

//        $client->request('GET', $this->url . '/get-all');
        $client->request('GET', '/api/v1/test');

        var_dump("\n\n\nFINAL NIZEJ:");
        var_dump($client->getResponse()->getStatusCode());
    }
}
