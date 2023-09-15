<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use Psr\Log\LoggerInterface;
use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class CacheCreator
{
    public function __construct(
        protected readonly LoggerInterface $logger,
        protected readonly string $redisUrl = 'redis://redis'
    ) {
    }

    public function create(string $prefix = ''): CacheProxy
    {
        return new CacheProxy(
            (new RedisAdapter(new Redis()))::createConnection($this->redisUrl),
            $this->logger,
            $prefix
        );
    }
}
