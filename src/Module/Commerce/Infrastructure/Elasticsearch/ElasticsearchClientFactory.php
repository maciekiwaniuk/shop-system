<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

final class ElasticsearchClientFactory
{
    /**
     * @throws AuthenticationException
     */
    public static function createClient(
        string $host,
        int $port,
        string $scheme
    ): Client {
        return ClientBuilder::create()
            ->setHosts(["{$scheme}://{$host}:{$port}"])
            ->build();
    }
}
