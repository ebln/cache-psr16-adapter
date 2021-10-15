<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter;

use Brnc\CachePsr16Adapter\CacheItemPool;
use Brnc\CachePsr16Adapter\Exception\InvalidArgumentException;
use Brnc\CachePsr16Adapter\Model\CacheItem;
use Brnc\CachePsr16Adapter\NowFactory;
use Brnc\tests\CachePsr16Adapter\helper\BrokenPsr16Mock;
use Brnc\tests\CachePsr16Adapter\helper\Psr16Array;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @internal
 * @covers \Brnc\CachePsr16Adapter\CacheItemPool
 */
final class CacheItemPoolTest extends TestCase
{
    public function testOnlyOwnItems(): void
    {
        $cache = new CacheItemPool(new Psr16Array());
        $item  = $this->createStub(CacheItemInterface::class);
        $this->expectException(InvalidArgumentException::class);
        // ensure that exception is implementing the right interface for the PSR-6
        // phpstan is quite strict: expects class-string<Throwable> yet PSR-6 doesn't implement throwable yet
        $implementing = class_implements(InvalidArgumentException::class);
        $implementing = $implementing ?: [];
        static::assertArrayHasKey(\Psr\Cache\InvalidArgumentException::class, $implementing);
        $cache->save($item);
    }

    public function testGetItemsWithFatalError(): void
    {
        $pool = new CacheItemPool(new BrokenPsr16Mock());
        static::assertSame([], $pool->getItems(['foo', 'bar']));
    }

    public function testFailingCommit(): void
    {
        $psr16 = $this->createStub(CacheInterface::class);
        $psr16->method('setMultiple')->willReturn(false);
        $pool = new CacheItemPool($psr16);
        $item = new CacheItem('foo', null, false, null, new NowFactory());
        $pool->saveDeferred($item);
        static::assertFalse($pool->commit());
    }
}
