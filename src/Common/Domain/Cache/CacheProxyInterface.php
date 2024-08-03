<?php

declare(strict_types=1);

namespace App\Common\Domain\Cache;

interface CacheProxyInterface
{
    public function exists(string $key): bool;

    public function get(string $key): ?string;

    public function set(string $key, string $value): bool;

    /**
     * @return array<string>
     */
    public function keysByPrefix(): array;

    /**
     * @param array<string> $keys
     */
    public function delByKeys(array $keys): bool;
}
