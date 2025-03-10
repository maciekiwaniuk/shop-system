<?php

declare(strict_types=1);

namespace App\Tests\Common\Infrastructure\Cache;

use App\Common\Infrastructure\Cache\CacheProxy;
use App\Tests\AbstractUnitTestCase;
use Exception;
use Psr\Log\LoggerInterface;
use Redis;

class CacheProxyTest extends AbstractUnitTestCase
{
    private Redis $cache;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cache = $this->createMock(Redis::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testExists(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('exists')
            ->with($this->callback(function ($key) {
                $this->assertEquals('examplePrefix.key', $key);
                return true;
            }))
            ->willReturn(true);
        $this->logger
            ->expects($this->never())
            ->method('error');

        $cacheProxy = new CacheProxy(
            $this->cache,
            $this->logger,
            'examplePrefix.',
        );

        $exists = $cacheProxy->exists('key');

        $this->assertEquals(
            true,
            $exists,
        );
    }

    public function testExistsThrowException(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('exists')
            ->willThrowException(new Exception());
        $this->logger
            ->expects($this->once())
            ->method('error');

        $cacheProxy = new CacheProxy(
            $this->cache,
            $this->logger,
            'prefix.',
        );

        $exists = $cacheProxy->exists('key');

        $this->assertEquals(
            false,
            $exists,
        );
    }
}
