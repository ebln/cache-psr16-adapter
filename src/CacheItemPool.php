<?php

declare(strict_types=1);

namespace Brnc\CachePsr16Adapter;

use Brnc\CachePsr16Adapter\Exception\InvalidArgumentException;
use Brnc\CachePsr16Adapter\Model\CacheItem;
use Brnc\CachePsr16Adapter\Model\SerializedItem;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

class CacheItemPool implements CacheItemPoolInterface
{
    private const DEFERED_TTL_NULL = '*NULL*';
    private CacheInterface $cache;
    /** @var array<string, SerializedItem> */
    private array      $defered = [];
    private NowFactory $nowFactory;

    public function __construct(CacheInterface $cache, ?NowFactory $nowFactory = null)
    {
        $this->cache      = $cache;
        $this->nowFactory = $nowFactory ?? new NowFactory();
    }

    public function __destruct()
    {
        if (!empty($this->defered)) {
            $this->commit();
        }
    }

    /** @psalm-param mixed $key */
    public function getItem($key): CacheItem
    {
        $this->validateKey($key);

        return $this->getItems([$key])[$key];
    }

    /**
     * @psalm-param array<mixed> $keys
     *
     * @return array<string, CacheItem>
     */
    public function getItems(array $keys = []): array
    {
        $items = [];

        try {
            foreach ($keys as $key) {
                $this->validateKey($key);

                if (isset($this->defered[$key])) {
                    $item = new CacheItem($key, $this->defered[$key]->getValue(), true, $this->defered[$key]->getExpiresAt());
                } else {
                    /** @var ?SerializedItem $rawItem */
                    $rawItem = $this->cache->get($key);
                    if ($rawItem instanceof SerializedItem) {
                        $item = new CacheItem($key, $rawItem->getValue(), true, $rawItem->getExpiresAt());
                    } else {
                        $item = new CacheItem($key, null, false, null);
                    }
                }

                $items[$key] = $item;
            }
        } catch (InvalidArgumentException $k) {
            throw $k;
        } catch (\Throwable $t) {
        }

        return $items;
    }

    /** @psalm-param mixed $key */
    public function hasItem($key): bool
    {
        $this->validateKey($key);

        try {
            // first test defered…
            if (isset($this->defered[$key])) {
                $expiry = $this->defered[$key]->getExpiresAt();
                if (null === $expiry || $expiry > $this->nowFactory->now()) {
                    return true; // report found if not expired
                }
                unset($this->defered[$key]); // remove defered
            }

            return $this->cache->has($key);
        } catch (\Throwable $t) {
        }

        return false;
    }

    public function clear(): bool
    {
        try {
            $this->defered = [];

            return $this->cache->clear();
        } catch (\Throwable $t) {
        }

        return false;
    }

    /** @psalm-param mixed $key */
    public function deleteItem($key): bool
    {
        $this->validateKey($key);

        try {
            unset($this->defered[$key]);

            return $this->cache->delete($key);
        } catch (\Throwable $t) {
        }

        return false;
    }

    /** @psalm-param array<array-key, mixed> $keys */
    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->validateKey($key);
            unset($this->defered[$key]);
        }

        try {
            return $this->cache->deleteMultiple($keys);
        } catch (\Throwable $t) {
        }

        return false;
    }

    public function save(CacheItemInterface $item): bool
    {
        $this->validateItem($item);

        try {
            return $this->cache->set($item->getKey(), new SerializedItem($item->getExpiry(), $item->get()), $this->getTimeToLive($item->getExpiry()));
        } catch (\Throwable $t) {
        }

        return false;
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->validateItem($item);
        $this->defered[$item->getKey()] = new SerializedItem($item->getExpiry(), $item->get());

        return true;
    }

    public function commit(): bool
    {
        try {
            $ttlMap = [];
            foreach ($this->defered as $key => $serializedValue) {
                $seconds = $this->getTimeToLive($serializedValue->getExpiresAt());
                $seconds ??= self::DEFERED_TTL_NULL;

                $ttlMap[$seconds][$key] = $serializedValue;
                unset($this->defered[$key]);
            }
            foreach ($ttlMap as $nullableSeconds => $rawData) {
                $seconds = self::DEFERED_TTL_NULL === $nullableSeconds ? null : $nullableSeconds;
                if (!$this->cache->setMultiple($rawData, $seconds)) {
                    return false;
                }
            }
        } catch (\Throwable $t) {
            return false;
        }

        return true;
    }

    /**
     * @psalm-assert CacheItem $item
     */
    private function validateItem(CacheItemInterface $item): void
    {
        if (!$item instanceof CacheItem) {
            throw new InvalidArgumentException('This cache pool «' . __CLASS__ . '» only supports its own items «' . CacheItem::class . '»');
        }

        $this->validateKey($item->getKey());
    }

    /**
     * @psalm-param mixed $key
     * @psalm-assert string $key
     */
    private function validateKey($key): void
    {
        if (!is_string($key) || '' === $key || preg_match('#[{}()/\\\\@:]#', $key)) {
            throw new InvalidArgumentException(
                'The provided key ' . (is_string($key) ? (' «' . $key . '»') : ('of type ' . gettype($key))) . ' is violating PSR-6!'
            );
        }
    }

    private function getTimeToLive(?\DateTimeImmutable $dateTime): ?int
    {
        if (null === $dateTime) {
            return null;
        }
        $stringSeconds = \DateTime::createFromFormat('U', '0')->add($this->nowFactory->now()->diff($dateTime))->format('U');

        return is_numeric($stringSeconds) ? (int)$stringSeconds : 0;
    }
}
