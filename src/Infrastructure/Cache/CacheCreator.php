<?php

namespace App\Infrastructure\Cache;

use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class CacheCreator
{
    protected readonly Redis $cache;

    public function __construct(
        protected readonly string $connection = 'redis://redis',
        string $prefix = '',
        int $expiry = 0
    ) {
        $redisAdapter = new RedisAdapter(
            new Redis(),
            $prefix,
            $expiry
        );
        $this->cache = $redisAdapter::createConnection($connection);
    }

    public function getCache(): Redis
    {
        return $this->cache;
    }
}