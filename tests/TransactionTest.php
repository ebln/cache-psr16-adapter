<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter;

use Brnc\CachePsr16Adapter\CacheItemPool;
use Brnc\CachePsr16Adapter\Model\CacheItem;
use Brnc\CachePsr16Adapter\Model\SerializedItem;
use Brnc\CachePsr16Adapter\NowFactory;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

/**
 * @internal
 * @covers \Brnc\CachePsr16Adapter\CacheItemPool
 */
final class TransactionTest extends TestCase
{
    /**
     * Ensures underlying PSR-16's `getMultiple()` is called not more than once per `getItems()`-call
     */
    public function testGetItems(): void
    {
        $psr16 = $this->createMock(CacheInterface::class);
        $psr16->expects(static::once())
            ->method('getMultiple')
            ->with(['foo', 'bar', 'far'])->willReturn(
                [
                    'foo' => null,
                    'bar' => ['bogus'],
                    'far' => new SerializedItem(new DateTimeImmutable('2045-12-12'), ['hello', 'world']),
                ]
            );

        $deferedItem = new CacheItem('baz', 'item 1', true, null, $this->getClock());
        $pool        = new CacheItemPool($psr16);
        $pool->saveDeferred($deferedItem);

        static::assertEquals(
            [
                'baz' => $deferedItem,
                'foo' => new CacheItem('foo', null, false, null, $this->getClock()),
                'bar' => new CacheItem('bar', null, false, null, $this->getClock()),
                'far' => new CacheItem('far', ['hello', 'world'], true, new DateTimeImmutable('2045-12-12'), $this->getClock()),
            ],
            $pool->getItems(['foo', 'bar', 'baz', 'far'])
        );
    }

    /**
     * Ensures underlying PSR-16's `getMultiple()` is never called
     */
    public function testGetItemsAllDefered(): void
    {
        $psr16 = $this->createMock(CacheInterface::class);
        $psr16->expects(static::never())->method('getMultiple');

        $item1 = new CacheItem('foo', 'item 1', true, null, $this->getClock());
        $item2 = new CacheItem('bar', 'item 2', true, null, $this->getClock());
        $pool  = new CacheItemPool($psr16);
        $pool->saveDeferred($item1);
        $pool->saveDeferred($item2);

        $gotItems = $pool->getItems(['foo', 'bar']);

        static::assertEquals(['foo' => $item1, 'bar' => $item2], $gotItems);
    }

    /**
     * Ensures underlying PSR-16's `deleteMultiple()` is only called once per `deleteItems()`-call
     */
    public function testDeleteItems(): void
    {
        $psr16 = $this->createMock(CacheInterface::class);
        $psr16->expects(static::once())
            ->method('deleteMultiple')
            ->with(['foo', 'bar'])->willReturn(true);

        $pool = new CacheItemPool($psr16);
        static::assertTrue($pool->deleteItems(['foo', 'bar']));
    }

    /**
     * Ensures underlying PSR-16's `setMultiple()` is only called once per TTL
     */
    public function testCommit(): void
    {
        $item1  = new CacheItem('foo', 'item 1', false, new DateTimeImmutable('2037-12-12'), $this->getClock());
        $item2  = new CacheItem('bar', 'item 2', false, null, $this->getClock());
        $item3  = new CacheItem('baz', 'item 3', false, new DateTimeImmutable('2037-12-12'), $this->getClock());
        $chunk1 = [
            'foo' => new SerializedItem($item1->getExpiry(), $item1->get()),
            'baz' => new SerializedItem($item3->getExpiry(), $item3->get()),
        ];
        $chunk2 = ['bar' => new SerializedItem($item2->getExpiry(), $item2->get())];

        $psr16 = $this->createMock(CacheInterface::class);
        $psr16->expects(static::exactly(2))
            ->method('setMultiple')
            ->withConsecutive([$chunk1, 187574400], [$chunk2, null])->willReturn(true);

        $pool = new CacheItemPool($psr16, $this->getFixedClock());
        $pool->saveDeferred($item1);
        $pool->saveDeferred($item2);
        $pool->saveDeferred($item3);

        static::assertTrue($pool->commit());
    }

    private function getClock(): NowFactory
    {
        return new NowFactory();
    }

    private function getFixedClock(): NowFactory
    {
        $stub = $this->createStub(NowFactory::class);
        $stub->method('now')->willReturn(new DateTimeImmutable('2031-12-31'));

        return $stub;
    }
}
