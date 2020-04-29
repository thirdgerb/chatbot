<?php


namespace Commune\Framework\Cache;


use Commune\Blueprint\Exceptions\HostRuntimeException;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * psr16 的异常.
 */
class Psr16InvalidArgsException
    extends HostRuntimeException
    implements InvalidArgumentException
{
}