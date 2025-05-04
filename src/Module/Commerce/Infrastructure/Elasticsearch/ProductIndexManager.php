<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Elasticsearch;

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
}
