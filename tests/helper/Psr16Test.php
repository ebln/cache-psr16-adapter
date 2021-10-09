<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter\helper;

use Cache\IntegrationTests\SimpleCacheTest;

class Psr16Test extends SimpleCacheTest
{

    public function createSimpleCache(): Psr16Array
    {
        return new Psr16Array();
    }
}
