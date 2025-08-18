<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Cache;

use App\Common\Domain\Cache\CacheCreatorInterface;
use App\Common\Domain\Cache\CacheProxyInterface;
use Psr\Log\LoggerInterface;
use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

readonly class CacheCreator implements CacheCreatorInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private string $redisUrl,
        private string $redisAppPrefix,
    ) {
    }

    public function create(string $prefix = ''): CacheProxyInterface
    {
        $adapter = new RedisAdapter(new Redis());
        return new CacheProxy(
            $adapter::createConnection($this->redisUrl),
            $this->logger,
            $this->redisAppPrefix . ':' . $prefix,
        );
    }
}
