<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter\helper;

use Cache\IntegrationTests\SimpleCacheTest;

/**
 * @internal
 * @covers \Brnc\tests\CachePsr16Adapter\helper\Psr16Array@covers \Brnc\tests\CachePsr16Adapter\helper\Psr16ArraySingleton
 * @codeCoverageIgnore
 */
final class Psr16Test extends SimpleCacheTest
{
    public function createSimpleCache(): Psr16Array
    {
        return new Psr16Array();
    }
}
