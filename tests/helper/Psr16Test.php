<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter\helper;

use Cache\IntegrationTests\SimpleCacheTest;

/**
 * @internal
 * @coversNothing
 */
final class Psr16Test extends SimpleCacheTest
{
    public function createSimpleCache(): Psr16Array
    {
        return new Psr16Array();
    }
}
