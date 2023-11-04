<?php

declare(strict_types=1);

namespace Module\Product\UI\Controller;

use App\Module\Product\Domain\Repository\ProductRepositoryInterface;
use App\Module\Product\Infrastructure\Doctrine\Generator\ProductGenerator;
use App\Tests\AbstractApplicationTestCase;
use Symfony\Component\HttpFoundation\Request;

class ProductsControllerTest extends AbstractApplicationTestCase
{
    protected string $url = '/api/v1/products';
    protected readonly ProductRepositoryInterface $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productRepository = self::getContainer()->get(ProductRepositoryInterface::class);
    }

    public function testGetAllAsUser(): void
    {
        $products = $this->productRepository->findAll();

        $client = $this->getUserClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/get-all'
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals($products[0]->getName(), $responseData['data'][0]['name']);
    }

    public function testCreateAsAdmin(): void
    {
        $productsCountBeforeAction = count($this->productRepository->findAll());

        $client = $this->getAdminClient();
        $client->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/create',
            content: json_encode([
                'name' => 'exampleProductName',
                'price' => 1999.99
            ])
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertCount(
            $productsCountBeforeAction + 1,
            $this->productRepository->findAll()
        );
    }

    public function testShowAsUser(): void
    {
        $product = (new ProductGenerator())->generate();
        $this->productRepository->save($product, true);

        $client = $this->getUserClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/show/' . $product->getSlug()
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals($product->getName(), $responseData['data']['name']);
    }
    
    public function testUpdateAsAdmin(): void
    {
        $product = (new ProductGenerator())->generate();
        $this->productRepository->save($product, true);

        $client = $this->getAdminClient();
        $client->request(
            method: Request::METHOD_PUT,
            uri: $this->url . '/update/' . $product->getId(),
            content: json_encode([
                'name' => 'newExampleName',
                'price' => 102.00
            ])
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $updatedProduct = $this->productRepository->findByUuid($product->getId());

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals('newExampleName', $updatedProduct->getName());
        $this->assertEquals(102.00, $updatedProduct->getPrice());
    }

    public function testDeleteAsAdmin(): void
    {
        $product = (new ProductGenerator())->generate();
        $this->productRepository->save($product, true);

        $client = $this->getAdminClient();
        $client->request(
            method: Request::METHOD_DELETE,
            uri: $this->url . '/delete/' . $product->getId()
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $deletedProduct = $this->productRepository->findByUuid($product->getId());

        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertNull($deletedProduct);
    }
}
