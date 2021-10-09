<?php

declare(strict_types=1);

namespace Brnc\CachePsr16Adapter\Model;

use Brnc\CachePsr16Adapter\Exception\InvalidArgumentException;
use DateInterval;
use DateTimeImmutable;
use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    private string             $key;
    private                    $value;
    private bool               $hit;
    private ?DateTimeImmutable $expiry;

    /**
     * @param string                 $key
     * @param mixed                  $value
     * @param bool                   $hit
     * @param null|DateTimeImmutable $expiry
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

    public function get()
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->hit;
    }

    public function set($value): self
    {
        $this->value = $value;
        $this->hit   = true;

        return $this;
    }

    public function expiresAt($expiration): self
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

    public function expiresAfter($time): self
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

    public function getValue()
    {
        return $this->value;
    }

    private function setExpiry(?DateTimeImmutable $expiry): void
    {
        $this->expiry = $expiry;
    }
}
