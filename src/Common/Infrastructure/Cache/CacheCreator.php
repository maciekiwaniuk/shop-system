<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Cache;

use Psr\Log\LoggerInterface;
use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

readonly class CacheCreator
{
    public function __construct(
        protected LoggerInterface $logger,
        protected string $redisUrl,
    ) {
    }

    public function create(string $prefix = ''): CacheProxy
    {
        return new CacheProxy(
            (new RedisAdapter(new Redis()))::createConnection($this->redisUrl),
            $this->logger,
            $prefix,
        );
    }
}
