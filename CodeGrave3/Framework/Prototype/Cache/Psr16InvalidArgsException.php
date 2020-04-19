<?php


namespace Commune\Framework\Prototype\Cache;

use Psr\SimpleCache\InvalidArgumentException;

/**
 * psr16 的异常.
 */
class Psr16InvalidArgsException extends \RuntimeException implements InvalidArgumentException
{
}