<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter;

use Brnc\CachePsr16Adapter\CacheItemPool;
use Brnc\tests\CachePsr16Adapter\helper\Psr16ArraySingleton;
use Cache\IntegrationTests\CachePoolTest;

/**
 * @internal
 * @covers \Brnc\CachePsr16Adapter\CacheItemPool
 * @covers \Brnc\tests\CachePsr16Adapter\helper\Psr16ArraySingleton
 * @covers \Brnc\CachePsr16Adapter\Model\CacheItem
 * @covers \Brnc\CachePsr16Adapter\Model\SerializedItem
 * @codeCoverageIgnore
 */
final class Psr6IntegrationTest extends CachePoolTest
{
    public function createCachePool(): CacheItemPool
    {
        return new CacheItemPool(Psr16ArraySingleton::getCache());
    }
}
