<?php

declare(strict_types=1);

namespace App\Tests\Integration\Module\Commerce\Infrastructure\Elasticsearch\Product;

use App\Module\Commerce\Application\DTO\Communication\ProductDTO;
use App\Module\Commerce\Infrastructure\Elasticsearch\ElasticsearchIndexException;
use App\Module\Commerce\Infrastructure\Elasticsearch\Product\ProductIndexManager;
use App\Tests\AbstractIntegrationTestCase;
use DateTimeImmutable;
use Elastic\Elasticsearch\Client;
use PHPUnit\Framework\Attributes\Test;

final class ProductIndexManagerTest extends AbstractIntegrationTestCase
{
    private Client $elasticsearchClient;
    private ProductIndexManager $productIndexManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->elasticsearchClient = self::getContainer()->get(Client::class);
        $this->productIndexManager = self::getContainer()->get(ProductIndexManager::class);
    }

    private function refreshIndex(): void
    {
        $this->elasticsearchClient->indices()->refresh(['index' => 'test_products']);
    }

    #[Test]
    public function it_throws_exception_when_tries_to_create_index_which_already_exists(): void
    {
        $this->expectException(ElasticsearchIndexException::class);
        $this->expectExceptionMessage('Product index already exists.');

        $this->productIndexManager->createIndex();
    }

    #[Test]
    public function it_indexes_and_searches_products(): void
    {
        $fancyProduct = new ProductDTO(1, 'Fancy product', 99.99, 'super-fancy-product', new DateTimeImmutable(), new DateTimeImmutable());
        $nikeAirMaxProduct = new ProductDTO(2, 'Nike Air Max', 49.50, 'nike-air-max', new DateTimeImmutable(), new DateTimeImmutable());
        $this->productIndexManager->indexProduct($fancyProduct);
        $this->productIndexManager->indexProduct($nikeAirMaxProduct);
        $this->refreshIndex();

        $fancyProductResult = $this->productIndexManager->searchByPhrase('fancy product');
        $nikeAirMaxProductResult = $this->productIndexManager->searchByPhrase('air max');

        $this->assertCount(1, $fancyProductResult);
        $this->assertSame('super-fancy-product', $fancyProductResult[0]['slug']);
        $this->assertCount(1, $nikeAirMaxProductResult);
        $this->assertSame('nike-air-max', $nikeAirMaxProductResult[0]['slug']);
    }

    #[Test]
    public function it_removes_product_from_index(): void
    {
        $product = new ProductDTO(1, 'Temporary Item', 10.00, 'temporary-item', new DateTimeImmutable(), new DateTimeImmutable());
        $this->productIndexManager->indexProduct($product);
        $this->refreshIndex();
        $resultsBefore = $this->productIndexManager->searchByPhrase('temporary');
        $this->productIndexManager->removeProduct(1);
        $this->refreshIndex();
        $resultsAfter = $this->productIndexManager->searchByPhrase('temporary');

        $this->assertCount(1, $resultsBefore);
        $this->assertCount(0, $resultsAfter);
    }
}