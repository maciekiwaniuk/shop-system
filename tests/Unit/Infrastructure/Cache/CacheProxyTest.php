<?php

namespace App\Tests\Unit\Infrastructure\Cache;

use App\Infrastructure\Cache\CacheProxy;
use App\Tests\Unit\AbstractUnitTestCase;
use Exception;
use Psr\Log\LoggerInterface;
use Redis;

class CacheProxyTest extends AbstractUnitTestCase
{
    protected Redis $cache;
    protected LoggerInterface $logger;
    protected CacheProxy $cacheProxy;

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

        $this->cacheProxy = new CacheProxy(
            $this->cache,
            $this->logger,
            'examplePrefix.'
        );

        $exists = $this->cacheProxy->exists('key');

        $this->assertEquals(
            true,
            $exists
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

        $this->cacheProxy = new CacheProxy(
            $this->cache,
            $this->logger,
            'prefix.'
        );

        $exists = $this->cacheProxy->exists('key');

        $this->assertEquals(
            false,
            $exists
        );
    }
}