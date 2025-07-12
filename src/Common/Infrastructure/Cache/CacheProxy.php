<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Cache;

use App\Common\Domain\Cache\CacheProxyInterface;
use Psr\Log\LoggerInterface;
use Redis;
use Throwable;

readonly class CacheProxy implements CacheProxyInterface
{
    public function __construct(
        private Redis $cache,
        private LoggerInterface $logger,
        private string $prefix,
    ) {
    }

    public function exists(string $key): bool
    {
        try {
            return (bool) $this->cache->exists($this->prefix . $key);
        } catch (Throwable $exception) {
            $this->logger->error('Failed to check cache key existence', [
                'key' => $key,
                'prefixed_key' => $this->prefix . $key,
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
            ]);
            return false;
        }
    }

    public function get(string $key): ?string
    {
        try {
            return $this->cache->get($this->prefix . $key);
        } catch (Throwable $exception) {
            $this->logger->error('Failed to retrieve value from cache', [
                'key' => $key,
                'prefixed_key' => $this->prefix . $key,
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
            ]);
            return null;
        }
    }

    public function set(string $key, string $value): bool
    {
        try {
            $this->cache->set($this->prefix . $key, $value);
            return true;
        } catch (Throwable $exception) {
            $this->logger->error('Failed to store value in cache', [
                'key' => $key,
                'prefixed_key' => $this->prefix . $key,
                'value_length' => strlen($value),
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
            ]);
            return false;
        }
    }

    /**
     * @return array<string>
     */
    public function keysByPrefix(): array
    {
        try {
            return $this->cache->keys($this->prefix . '*');
        } catch (Throwable $exception) {
            $this->logger->error('Failed to retrieve cache keys by prefix', [
                'prefix' => $this->prefix,
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
            ]);
            return [];
        }
    }

    /**
     * @param array<string> $keys
     */
    public function delByKeys(array $keys): bool
    {
        try {
            $this->cache->del(array_map(
                fn($key) => $this->prefix . $key,
                $keys,
            ));
            return true;
        } catch (Throwable $exception) {
            $this->logger->error('Failed to delete cache keys', [
                'keys' => $keys,
                'prefixed_keys' => array_map(fn($key) => $this->prefix . $key, $keys),
                'keys_count' => count($keys),
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
            ]);
            return false;
        }
    }
}
