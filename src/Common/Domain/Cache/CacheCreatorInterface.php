<?php

declare(strict_types=1);

namespace App\Common\Domain\Cache;

interface CacheCreatorInterface
{
    public function create(string $prefix): CacheProxyInterface;
}
