<?php

declare(strict_types=1);

namespace Brnc\CachePsr16Adapter\Model;

use Brnc\CachePsr16Adapter\Exception\InvalidArgumentException;
use DateInterval;
use DateTimeImmutable;
use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    /** @psalm-var mixed */
    private $value;
    private string             $key;
    private bool               $hit;
    private ?DateTimeImmutable $expiry;

    /**
     * @psalm-param mixed $value
     */
    public function __construct(string $key, $value, bool $hit, ?DateTimeImmutable $expiry)
    {
        $this->key    = $key;
        $this->value  = $value;
        $this->hit    = $hit;
        $this->expiry = $expiry;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @psalm-return mixed
     */
    public function get()
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->hit;
    }

    /**
     * @psalm-param mixed $value
     */
    public function set($value)
    {
        $this->value = $value;
        $this->hit   = true;

        return $this;
    }

    /**
     * @param null|\DateTimeInterface $expiration
     *
     * @psalm-param mixed             $expiration
     */
    public function expiresAt($expiration)
    {
        if (null === $expiration) {
            $this->setExpiry(null);
        } elseif ($expiration instanceof \DateTimeInterface) {
            // Refactor to createFromInterface;
            if ($expiration instanceof DateTimeImmutable) {
                $this->setExpiry($expiration);
            } else {
                $this->setExpiry(DateTimeImmutable::createFromMutable($expiration));
            }
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    /**
     * @param null|DateInterval|int $time
     *
     * @psalm-param mixed           $time
     */
    public function expiresAfter($time)
    {
        if (null === $time) {
            $this->setExpiry(null);
        } elseif ($time instanceof DateInterval) {
            $this->setExpiry((new DateTimeImmutable())->add($time));
        } elseif (is_int($time)) {
            $this->setExpiry((new DateTimeImmutable())->add(new DateInterval('PT' . $time . 'S')));
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function getExpiry(): ?DateTimeImmutable
    {
        return $this->expiry;
    }

    private function setExpiry(?DateTimeImmutable $expiry): void
    {
        $this->expiry = $expiry;
    }
}
