<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter\helper;

use DateInterval;
use DateTime;
use Psr\SimpleCache\CacheInterface;

class Psr16Array implements CacheInterface
{
    private array $values = [];
    private array $ttlMap = [];

    public function get($key, $default = null)
    {
        $this->validateKey($key);
        $this->revoke();

        return $this->has($key) ? unserialize($this->values[$key]) : $default;
    }

    public function set($key, $value, $ttl = null)
    {
        $this->validateKey($key);
        $this->values[$key] = serialize($value);
        $expiryEpoch        = $this->getEpoch($ttl);
        if (null !== $expiryEpoch) {
            $this->ttlMap[$expiryEpoch][] = $key;
        }

        return true;
    }

    public function delete($key)
    {
        $this->validateKey($key);
        unset($this->values[$key]);

        return true;
    }

    public function clear()
    {
        $this->values = [];
        $this->ttlMap = [];

        return true;
    }

    public function getMultiple($keys, $default = null)
    {
        if (!is_iterable($keys)) {
            throw new Psr16InvalidArgumentException();
        }
        $return = [];
        foreach ($keys as $key) {
            $return[$key] = $this->get($key, $default);
        }

        return $return;
    }

    public function setMultiple($values, $ttl = null)
    {
        if (!is_iterable($values)) {
            throw new Psr16InvalidArgumentException();
        }
        foreach ($values as $key => $value) {
            $key = is_int($key) ? (string)$key : $key; // sanitize ['0' => 'value0']

            $this->set($key, $value, $ttl);
        }

        return true;
    }

    public function deleteMultiple($keys)
    {
        if (!is_iterable($keys)) {
            throw new Psr16InvalidArgumentException();
        }
        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                return false;
            }
        }

        return true;
    }

    public function has($key)
    {
        $this->validateKey($key);
        $this->revoke();

        return array_key_exists($key, $this->values);
    }

    private function validateKey($key): void
    {
        if (!is_string($key) || '' === $key || preg_match('#[{}()/\\\\@:]#', $key)) {
            throw new Psr16InvalidArgumentException();
        }
    }

    private function revoke(): void
    {
        $nowEpoch = time();
        foreach ($this->ttlMap as $epoch => $keys) {
            if ($nowEpoch > $epoch) {
                foreach ($keys as $key) {
                    unset($this->values[$key]);
                }
                unset($this->ttlMap[$epoch]);
            } else {
                // clean up orphaned TTL mapping
                $orphaned = array_diff_key(array_flip($keys), $this->values);
                foreach ($orphaned as $key) {
                    unset($this->ttlMap[$epoch][$key]);
                }
            }
        }
    }

    /**
     * @param null|DateInterval|int $ttl
     */
    private function getEpoch($ttl): ?int
    {
        if (null === $ttl) {
            return PHP_INT_MAX; // TODO default TTL!
        }
        if ($ttl instanceof DateInterval) {
            return (new DateTime())->add($ttl)->getTimestamp();
        }
        if (!is_string($ttl) && is_int($ttl)) {
            if ($ttl <= 0) {
                return 0;
            }
            $now = time();
            if ($ttl > PHP_INT_MAX - $now) {
                return PHP_INT_MAX;
            }

            return $now + $ttl;
        }

        throw new Psr16InvalidArgumentException();
    }
}
