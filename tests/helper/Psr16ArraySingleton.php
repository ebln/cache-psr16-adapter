<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter\helper;

class Psr16ArraySingleton
{
    static private ?Psr16Array $instance = null;

    static public function getCache(): Psr16Array
    {
        if (null === self::$instance) {
            self::$instance = new Psr16Array();
        }

        return self::$instance;
    }
}
