<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Interface\Controller;

use App\Module\Commerce\Domain\Repository\ProductRepositoryInterface;
use App\Tests\Module\Commerce\AbstractApplicationCommerceTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Group('application')]
class ProductsControllerTest extends AbstractApplicationCommerceTestCase
{
    private string $url = '/api/v1/products';
    private ProductRepositoryInterface $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productRepository = self::getContainer()->get(ProductRepositoryInterface::class);
    }

    #[Test]
    public function can_get_paginated_products_as_client(): void
    {
        $this->insertProduct();
        $products = $this->productRepository->getPaginatedById(offset: 1, limit: 10);
        $clientBrowser = $this->getClientBrowser();

        $clientBrowser->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/get-paginated',
            parameters: [
                'offset' => 1,
                'limit' => 10,
            ],
        );

        $responseData = json_decode($clientBrowser->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals($products[0]->getName(), $responseData['data'][0]['name']);
        $this->assertEquals(count($products), count($responseData['data']));
    }

    #[Test]
    public function can_create_product_as_admin(): void
    {
        $productsCountBeforeAction = count($this->productRepository->getPaginatedById());
        $adminBrowser = $this->getAdminBrowser();

        $adminBrowser->request(
            method: Request::METHOD_POST,
            uri: $this->url . '/create',
            content: json_encode([
                'name' => 'exampleProductName',
                'price' => 1999.99,
            ]),
        );

        $responseData = json_decode($adminBrowser->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertCount(
            $productsCountBeforeAction + 1,
            $this->productRepository->getPaginatedById(),
        );
    }

    #[Test]
    public function can_show_product_as_guest(): void
    {
        $product = $this->insertProduct();
        $guestBrowser = $this->getGuestBrowser();

        $guestBrowser->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/show/' . $product->getSlug(),
        );

        $responseData = json_decode($guestBrowser->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals($product->getName(), $responseData['data']['name']);
    }

    #[Test]
    public function can_update_product_as_admin(): void
    {
        $product = $this->insertProduct();
        $adminBrowser = $this->getAdminBrowser();

        $adminBrowser->request(
            method: Request::METHOD_PUT,
            uri: $this->url . '/update/' . $product->getId(),
            content: json_encode([
                'name' => 'newExampleName',
                'price' => 102.00,
            ]),
        );

        $responseData = json_decode($adminBrowser->getResponse()->getContent(), true);
        $updatedProduct = $this->productRepository->findById($product->getId());
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertEquals('newExampleName', $updatedProduct->getName());
        $this->assertEquals(102.00, $updatedProduct->getPrice());
    }

    #[Test]
    public function can_delete_product_as_admin(): void
    {
        $product = $this->insertProduct();
        $adminBrowser = $this->getAdminBrowser();

        $adminBrowser->request(
            method: Request::METHOD_DELETE,
            uri: $this->url . '/delete/' . $product->getId(),
        );

        $responseData = json_decode($adminBrowser->getResponse()->getContent(), true);
        $deletedProduct = $this->productRepository->findBySlug($product->getSlug());
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertNull($deletedProduct);
    }

    #[Test]
    public function can_search_products_as_guest(): void
    {
        $product = $this->insertProduct();
        $guestBrowser = $this->getGuestBrowser();

        $guestBrowser->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/search',
            parameters: [
                'phrase' => $product->getName(),
            ],
        );

        $responseData = json_decode($guestBrowser->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
    }

    #[Test]
    public function can_not_search_with_invalid_phrase_as_guest(): void
    {
        $guestBrowser = $this->getGuestBrowser();

        $guestBrowser->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/search',
            parameters: [
                'phrase' => 'a',
            ],
        );

        $responseData = json_decode($guestBrowser->getResponse()->getContent(), true);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('errors', $responseData);
    }

    #[Test]
    public function can_not_search_for_empty_phrase_as_guest(): void
    {
        $guestBrowser = $this->getGuestBrowser();

        $guestBrowser->request(
            method: Request::METHOD_GET,
            uri: $this->url . '/search',
            parameters: [
                'phrase' => '',
            ],
        );

        $responseData = json_decode($guestBrowser->getResponse()->getContent(), true);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('errors', $responseData);
    }
}
