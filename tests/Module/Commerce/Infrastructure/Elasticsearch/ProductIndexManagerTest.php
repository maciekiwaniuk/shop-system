<?php

declare(strict_types=1);

namespace App\Tests\Integration\Module\Commerce\Infrastructure\Elasticsearch;

use App\Common\Application\Bus\SyncCommandBus\SyncCommandBusInterface;
use App\Module\Commerce\Application\Command\CreateProduct\CreateProductCommand;
use App\Module\Commerce\Application\DTO\Validation\CreateProductDTO;
use App\Module\Commerce\Domain\Entity\Product;
use App\Module\Commerce\Domain\Repository\ProductRepositoryInterface;
use App\Module\Commerce\Infrastructure\Elasticsearch\ElasticsearchIndexException;
use App\Module\Commerce\Infrastructure\Elasticsearch\ProductIndexManager;
use App\Tests\AbstractIntegrationTestCase;
use Elastic\Elasticsearch\Client;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('integration')]
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
        $fancyProduct = $this->persistProduct(new Product('Fancy product', 99.99));
        $nikeAirMaxProduct = $this->persistProduct(new Product('Nike Air Max', 3.23));
        $this->productIndexManager->indexProduct($fancyProduct);
        $this->productIndexManager->indexProduct($nikeAirMaxProduct);
        $this->refreshIndex();

        $fancyProductResult = $this->productIndexManager->searchByPhrase('fancy product');
        $nikeAirMaxProductResult = $this->productIndexManager->searchByPhrase('air max');

        $this->assertCount(1, $fancyProductResult);
        $this->assertSame($fancyProduct->getSlug(), $fancyProductResult[0]['slug']);
        $this->assertCount(1, $nikeAirMaxProductResult);
        $this->assertSame($nikeAirMaxProduct->getSlug(), $nikeAirMaxProductResult[0]['slug']);
    }

    #[Test]
    public function it_removes_product_from_index(): void
    {
        $product = $this->persistProduct(new Product('Temporary Item', 10.00));
        $this->productIndexManager->indexProduct($product);
        $this->refreshIndex();
        $resultsBefore = $this->productIndexManager->searchByPhrase($product->getName());
        $this->productIndexManager->removeProduct($product->getId());
        $this->refreshIndex();
        $resultsAfter = $this->productIndexManager->searchByPhrase($product->getName());

        $this->assertCount(1, $resultsBefore);
        $this->assertCount(0, $resultsAfter);
    }

    private function persistProduct(Product $product): Product
    {
        $createProductDto = new CreateProductDTO($product->getName(), $product->getPrice());
        $createProductCommand = new CreateProductCommand($createProductDto);
        $syncCommandBus = self::getContainer()->get(SyncCommandBusInterface::class);
        $commandResult = $syncCommandBus->handle($createProductCommand);

        $productRepository = self::getContainer()->get(ProductRepositoryInterface::class);
        return $productRepository->getReference($commandResult->entityId);
    }
}
