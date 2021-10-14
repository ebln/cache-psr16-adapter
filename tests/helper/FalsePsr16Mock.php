<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter\helper;

class FalsePsr16Mock extends Psr16Array
{
    public function set($key, $value, $ttl = null): bool
    {
        return false;
    }

    public function delete($key): bool
    {
        return false;
    }

    public function clear(): bool
    {
        return false;
    }

    /**
     * @param mixed[]    $values
     * @param null|mixed $ttl
     */
    public function setMultiple($values, $ttl = null): bool
    {
        return false;
    }

    /** @param string[] $keys */
    public function deleteMultiple($keys): bool
    {
        return false;
    }
}
