<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter;

use Brnc\CachePsr16Adapter\CacheItemPool;
use Brnc\tests\CachePsr16Adapter\helper\Psr16ArraySingleton;
use Cache\IntegrationTests\CachePoolTest;

class Psr6IntegrationTest extends CachePoolTest
{

    public function createCachePool(): CacheItemPool
    {
        return new CacheItemPool(Psr16ArraySingleton::getCache());
    }
}
