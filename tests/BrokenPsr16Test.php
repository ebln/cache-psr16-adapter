<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter;

use Brnc\CachePsr16Adapter\CacheItemPool;
use Brnc\CachePsr16Adapter\Model\CacheItem;
use Brnc\CachePsr16Adapter\NowFactory;
use Brnc\tests\CachePsr16Adapter\helper\BrokenPsr16Mock;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \Brnc\CachePsr16Adapter\CacheItemPool
 */
final class BrokenPsr16Test extends TestCase
{
    public function testHasItem(): void
    {
        $cache = $this->createCache();
        static::assertFalse($cache->hasItem('foo'));
    }

    public function testClear(): void
    {
        $cache = $this->createCache();
        static::assertFalse($cache->clear());
    }

    public function testCommit(): void
    {
        $cache = $this->createCache();
        $cache->saveDeferred($this->createItem());
        static::assertFalse($cache->commit());
    }

    public function testSave(): void
    {
        $cache = $this->createCache();
        static::assertFalse($cache->save($this->createItem()));
    }

    public function testDeleteItem(): void
    {
        $cache = $this->createCache();
        static::assertFalse($cache->deleteItem('foo'));
    }

    public function testDeleteItems(): void
    {
        $cache = $this->createCache();
        static::assertFalse($cache->deleteItems(['foo']));
    }

    private function createItem(): CacheItem
    {
        return new CacheItem('foo_' . bin2hex(random_bytes(2)), bin2hex(random_bytes(4)), true, null, new NowFactory());
    }

    private function createCache(): CacheItemPool
    {
        return new CacheItemPool(new BrokenPsr16Mock());
    }
}
