<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Cache;

use Psr\Log\LoggerInterface;
use Redis;
use Throwable;

class CacheProxy
{
    public function __construct(
        protected readonly Redis $cache,
        protected readonly LoggerInterface $logger,
        protected readonly string $prefix
    ) {
    }

    public function exists(string $key): bool
    {
        try {
            return (bool) $this->cache->exists($this->prefix . $key);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return false;
        }
    }

    public function get(string $key): ?string
    {
        try {
            return $this->cache->get($this->prefix . $key);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return null;
        }
    }

    public function set(string $key, string $value): bool
    {
        try {
            $this->cache->set($this->prefix . $key, $value);
            return true;
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return false;
        }
    }

    /**
     * @return array<string>
     */
    public function keysByPrefix(): array
    {
        try {
            return $this->cache->keys($this->prefix);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return [];
        }
    }

    public function del(array $keys): bool
    {
        try {
            $this->cache->del(array_map(
                fn($key) => $this->prefix . $key,
                $keys
            ));
            return true;
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return false;
        }
    }
}
