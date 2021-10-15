<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter\helper;

use Cache\IntegrationTests\SimpleCacheTest;

/**
 * @internal
 * @covers \Brnc\tests\CachePsr16Adapter\helper\Psr16Array
 * @covers \Brnc\tests\CachePsr16Adapter\helper\Psr16ArraySingleton
 * @codeCoverageIgnore
 */
final class Psr16Test extends SimpleCacheTest
{
    public function createSimpleCache(): Psr16Array
    {
        return new Psr16Array();
    }

    /* just for coverage, TTL cannot be read back and Psr16Array has no injected clock */
    public function testOverlyLargeTtl(): void
    {
        $cache = $this->createSimpleCache();
        static::assertTrue($cache->set('foo', 'bar', PHP_INT_MAX - (int)(time() / 2)));
    }
}
