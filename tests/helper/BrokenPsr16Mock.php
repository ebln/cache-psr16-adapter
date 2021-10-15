<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter\helper;

use Psr\SimpleCache\CacheInterface;

/**
 * @SuppressWarnings(PHPMD)
 * @codeCoverageIgnore
 */
class BrokenPsr16Mock implements CacheInterface
{
    /**
     * @param string $key
     * @param null   $default
     */
    public function get($key, $default = null): bool
    {
        throw new \RuntimeException();
    }

    public function set($key, $value, $ttl = null): bool
    {
        throw new \RuntimeException();
    }

    public function delete($key): bool
    {
        throw new \RuntimeException();
    }

    public function clear(): bool
    {
        throw new \RuntimeException();
    }

    /**
     * @param string[]   $keys
     * @param null|mixed $default
     *
     * @return mixed[]
     */
    public function getMultiple($keys, $default = null)
    {
        throw new \RuntimeException();
    }

    /** @param mixed[] $values */
    public function setMultiple($values, $ttl = null): bool
    {
        throw new \RuntimeException();
    }

    /** @param string[] $keys */
    public function deleteMultiple($keys): bool
    {
        throw new \RuntimeException();
    }

    public function has($key): bool
    {
        throw new \RuntimeException();
    }
}
