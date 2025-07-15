<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Elasticsearch;

use App\Module\Commerce\Application\DTO\Communication\ProductDTO;
use App\Module\Commerce\Domain\Entity\Product;
use App\Module\Commerce\Domain\Repository\ProductSearchRepositoryInterface;
use App\Module\Commerce\Infrastructure\Elasticsearch\ElasticsearchIndexException;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

final readonly class ProductIndexManager implements ProductSearchRepositoryInterface
{
    private const string INDEX_NAME = 'products';

    public function __construct(
        private Client $elasticsearchClient,
        private string $environment,
    ) {
    }

    public function createIndex(): void
    {
        if ($this->checkIfIndexExists()) {
            throw new ElasticsearchIndexException('Product index already exists.');
        }

        $params = [
            'index' => $this->getIndexName(),
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 1,
                ],
                'mappings' => [
                    'properties' => [
                        'name' => [
                            'type' => 'text',
                            'analyzer' => 'standard',
                        ],
                        'price' => [
                            'type' => 'float',
                        ],
                        'slug' => [
                            'type' => 'keyword',
                        ],
                        'createdAt' => [
                            'type' => 'date',
                        ],
                        'updatedAt' => [
                            'type' => 'date',
                        ],
                    ],
                ],
            ],
        ];

        $this->elasticsearchClient->indices()->create($params);
    }

    public function indexProduct(Product $product): void
    {
        var_dump($product->getId());

        $this->elasticsearchClient->index([
            'index' => $this->getIndexName(),
            'id' => (string) $product->getId(),
            'body' => [
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'slug' => $product->getSlug(),
                'createdAt' => $product->getCreatedAt()->format('c'),
                'updatedAt' => $product->getUpdatedAt()->format('c'),
            ],
        ]);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function removeProduct(int $id): void
    {
        $this->elasticsearchClient->delete([
            'index' => $this->getIndexName(),
            'id' => (string) $id,
        ]);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @return array<int, array<string, mixed>>
     */
    public function searchByPhrase(string $phrase): array
    {
        $params = [
            'index' => $this->getIndexName(),
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            [
                                'match_phrase_prefix' => [
                                    'name' => [
                                        'query' => $phrase,
                                        'max_expansions' => 50,
                                        'slop' => 1,
                                    ],
                                ],
                            ],
                            [
                                'match' => [
                                    'name' => [
                                        'query' => $phrase,
                                        'fuzziness' => 'AUTO',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '_source' => ['name', 'price', 'slug', 'createdAt', 'updatedAt'],
                'size' => 6,
            ],
        ];

        $results = $this->elasticsearchClient->search($params);
        return array_map(function ($hit) {
            return $hit['_source'];
        }, $results['hits']['hits']);
    }

    private function getIndexName(): string
    {
        if ($this->environment === 'test') {
            return 'test_' . self::INDEX_NAME;
        };
        return self::INDEX_NAME;
    }

    private function checkIfIndexExists(): bool
    {
        return $this->elasticsearchClient
            ->indices()
            ->exists(['index' => $this->getIndexName()])
            ->asBool();
    }
}
