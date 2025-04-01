<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Interface\Controller;

use App\Module\Commerce\Domain\Repository\ProductRepositoryInterface;
use App\Module\Commerce\Infrastructure\Doctrine\Generator\ProductGenerator;
use App\Tests\AbstractApplicationTestCase;
use App\Tests\Module\Commerce\AbstractApplicationCommerceTestCase;
use Symfony\Component\HttpFoundation\Request;

class ProductsControllerTest extends AbstractApplicationCommerceTestCase
{
    private string $url = '/api/v1/products';
    private ProductRepositoryInterface $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productRepository = self::getContainer()->get(ProductRepositoryInterface::class);
    }

    /** @test */
    public function can_get_paginated_products_as_user(): void
    {
        $product = new ProductGenerator()->generate();
        $this->productRepository->save($product, true);
        $products = $this->productRepository->getPaginatedById(offset: 1, limit: 10);

        $client = $this->getClientBrowser();
        $client->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/get-paginated',
            parameters: [
                'offset' => 1,
                'limit' => 10,
            ],
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals($products[0]->getName(), $responseData['data'][0]['name']);
        $this->assertEquals(count($products), count($responseData['data']));
    }

    /** @test */
    public function can_create_product_as_admin(): void
    {
        $productsCountBeforeAction = count($this->productRepository->getPaginatedById());

        $client = $this->getAdminBrowser();
        $client->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/create',
            content: json_encode([
                'name' => 'exampleProductName',
                'price' => 1999.99,
            ]),
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertCount(
            $productsCountBeforeAction + 1,
            $this->productRepository->getPaginatedById(),
        );
    }

    public function testShowAsUser(): void
    {
        $product = new ProductGenerator()->generate();
        $this->productRepository->save($product, true);

        $client = $this->getClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/show/' . $product->getSlug(),
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals($product->getName(), $responseData['data']['name']);
    }

    public function testUpdateAsAdmin(): void
    {
        $product = new ProductGenerator()->generate();
        $this->productRepository->save($product, true);

        $client = $this->getAdminBrowser();
        $client->request(
            method: Request::METHOD_PUT,
            uri: $this->url . '/update/' . $product->getId(),
            content: json_encode([
                'name' => 'newExampleName',
                'price' => 102.00,
            ]),
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $updatedProduct = $this->productRepository->findById($product->getId());

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals('newExampleName', $updatedProduct->getName());
        $this->assertEquals(102.00, $updatedProduct->getPrice());
    }

    public function testDeleteAsAdmin(): void
    {
        $product = new ProductGenerator()->generate();
        $this->productRepository->save($product, true);

        $client = $this->getAdminBrowser();
        $client->request(
            method: Request::METHOD_DELETE,
            uri: $this->url . '/delete/' . $product->getId(),
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $deletedProduct = $this->productRepository->findBySlug($product->getSlug());

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertNull($deletedProduct);
    }
}
