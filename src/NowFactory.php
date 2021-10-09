<?php

declare(strict_types=1);

namespace Brnc\CachePsr16Adapter;

use DateTimeImmutable;

/**
 * TODO Replace with PSR-20 → Clock
 */
class NowFactory
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
