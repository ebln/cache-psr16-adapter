<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter\helper;

use InvalidArgumentException as SplException;
use Psr\SimpleCache\InvalidArgumentException as Psr16Exception;

class Psr16InvalidArgumentException extends SplException implements Psr16Exception
{
}
