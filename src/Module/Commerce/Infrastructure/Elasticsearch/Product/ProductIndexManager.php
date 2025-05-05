<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Elasticsearch\Product;

use App\Module\Commerce\Application\DTO\Communication\ProductDTO;
use App\Module\Commerce\Infrastructure\Elasticsearch\ElasticsearchIndexException;
use Elastic\Elasticsearch\Client;

final readonly class ProductIndexManager
{
    private const string INDEX_NAME = 'products';

    public function __construct(
        private Client $elasticsearchClient,
    ) {
    }

    public function createIndex(): void
    {
        if ($this->elasticsearchClient->indices()->exists(['index' => self::INDEX_NAME])) {
            throw new ElasticsearchIndexException('Product index already exists.');
        }

        $params = [
            'index' => self::INDEX_NAME,
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

    public function indexProduct(ProductDTO $dto): void
    {
        $this->elasticsearchClient->index([
            'index' => self::INDEX_NAME,
            'id' => $dto->id,
            'body' => [
                'name' => $dto->name,
                'price' => $dto->price,
                'slug' => $dto->slug,
                'createdAt' => $dto->createdAt->format('c'),
                'updatedAt' => $dto->updatedAt->format('c'),
            ]
        ]);
    }

    public function removeProduct(int $id): void
    {
        $this->elasticsearchClient->delete([
            'index' => self::INDEX_NAME,
            'id' => $id,
        ]);
    }

    public function searchByPhrase(string $phrase): array
    {
        $params = [
            'index' => self::INDEX_NAME,
            'body' => [
                'query' => [
                    'match_phrase_prefix' => [
                        'name' => [
                            'query' => $phrase,
                            'max_expansions' => 50,
                            'slop' => 1,
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
}
