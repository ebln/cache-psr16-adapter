<?php

declare(strict_types=1);

namespace Brnc\CachePsr16Adapter\Model;

use DateTimeImmutable;

class SerializedItem
{
    private $value;
    private ?DateTimeImmutable $expiresAt;

    public function __construct(?DateTimeImmutable $expiresAt, $value)
    {
        $this->expiresAt = $expiresAt;
        $this->value     = $value;
    }

    /** @return mixed */
    public function getValue()
    {
        return $this->value;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }
}
