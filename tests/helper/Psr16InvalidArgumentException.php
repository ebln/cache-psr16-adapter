<?php

declare(strict_types=1);

namespace Brnc\tests\CachePsr16Adapter\helper;

use Psr\SimpleCache\InvalidArgumentException as Psr16Exception;
use InvalidArgumentException as SplException;

class Psr16InvalidArgumentException extends SplException implements Psr16Exception
{

}
