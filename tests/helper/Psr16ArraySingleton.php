<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter\helper;

class Psr16ArraySingleton
{
    private static ?Psr16Array $instance = null;

    public static function getCache(): Psr16Array
    {
        if (null === self::$instance) {
            self::$instance = new Psr16Array();
        }

        return self::$instance;
    }
}
