<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter;

use Brnc\CachePsr16Adapter\Model\CacheItem;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CacheItemTest extends TestCase
{
    public function testExpiresAtWithDateTime(): void
    {
        $item     = new CacheItem('foo', null, false, null);
        $dateTime = new \DateTime();
        $item->expiresAt($dateTime);

        static::assertEquals($dateTime, $item->getExpiry());
        static::assertNotSame($dateTime, $item->getExpiry());
    }

    public function testExpiresAtWithDateTimeImmutable(): void
    {
        $item     = new CacheItem('foo', null, false, null);
        $dateTime = new \DateTimeImmutable();
        $item->expiresAt($dateTime);
        static::assertSame($dateTime, $item->getExpiry());
    }
}
