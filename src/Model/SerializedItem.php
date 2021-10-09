<?php

declare(strict_types=1);

namespace Brnc\CachePsr16Adapter\Model;

use DateTimeImmutable;

class SerializedItem
{
    /** @psalm-var mixed */
    private $value;
    private ?DateTimeImmutable $expiresAt;

    /**
     * @psalm-param mixed $value
     */
    public function __construct(?DateTimeImmutable $expiresAt, $value)
    {
        $this->expiresAt = $expiresAt;
        $this->value     = $value;
    }

    /** @psalm-return mixed */
    public function getValue()
    {
        return $this->value;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }
}
