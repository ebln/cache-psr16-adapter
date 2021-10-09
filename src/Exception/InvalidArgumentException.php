<?php

declare(strict_types=1);

namespace Brnc\CachePsr16Adapter\Exception;

use InvalidArgumentException as SplException;
use Psr\Cache\InvalidArgumentException as Psr6Exception;

class InvalidArgumentException extends SplException implements Psr6Exception
{
}
