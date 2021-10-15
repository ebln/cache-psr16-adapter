<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter;

use Brnc\CachePsr16Adapter\Exception\InvalidArgumentException;
use Brnc\CachePsr16Adapter\Model\CacheItem;
use Brnc\CachePsr16Adapter\NowFactory;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \Brnc\CachePsr16Adapter\Model\CacheItem
 */
final class CacheItemTest extends TestCase
{
    public function testExpiresAtWithDateTime(): void
    {
        $item     = new CacheItem('foo', null, false, null, $this->getClock());
        $dateTime = new DateTime();
        $item->expiresAt($dateTime);

        static::assertEquals($dateTime, $item->getExpiry());
        static::assertNotSame($dateTime, $item->getExpiry());
    }

    public function testExpiresAtWithDateTimeImmutable(): void
    {
        $item     = new CacheItem('foo', null, false, null, $this->getClock());
        $dateTime = new DateTimeImmutable();
        $item->expiresAt($dateTime);
        static::assertSame($dateTime, $item->getExpiry());
    }

    public function testExpiresAtWithBogus(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $implementing = class_implements(InvalidArgumentException::class);
        $implementing = $implementing ?: [];
        static::assertArrayHasKey(\Psr\Cache\InvalidArgumentException::class, $implementing);
        $item = new CacheItem('foo', null, false, null, $this->getClock());
        $item->expiresAt((object)['foo']);
    }

    public function testExpiresAtWithNull(): void
    {
        $item = new CacheItem('foo', null, false, null, $this->getClock());
        $item->expiresAt(null);
        static::assertNull($item->getExpiry());
    }

    public function testExpiresAfterWithNull(): void
    {
        $item = new CacheItem('foo', null, false, null, $this->getClock());
        $item->expiresAfter(null);
        static::assertNull($item->getExpiry());
    }

    public function testExpiresAfterWithBogus(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $item = new CacheItem('foo', null, false, null, $this->getClock());
        $item->expiresAfter('foobar');
    }

    public function testExpiresAfterWithInt(): void
    {
        $item = new CacheItem('foo', null, false, null, $this->getFixedClock());
        $item->expiresAfter(60);
        static::assertEquals($this->getFixedClock()->now()->add(new DateInterval('PT60S')), $item->getExpiry());
    }

    public function testExpiresAfterWithDateInterval(): void
    {
        $item     = new CacheItem('foo', null, false, null, $this->getFixedClock());
        $interval = new DateInterval('PT60S');
        $item->expiresAfter($interval);
        static::assertEquals($this->getFixedClock()->now()->add(new DateInterval('PT60S')), $item->getExpiry());
    }

    private function getClock(): NowFactory
    {
        return new NowFactory();
    }

    private function getFixedClock(): NowFactory
    {
        $stub = $this->createStub(NowFactory::class);
        $stub->method('now')
            ->willReturn(new DateTimeImmutable('2037-12-31'))
        ;

        return $stub;
    }
}
