<?php


namespace Commune\Framework\Cache;


use Commune\Blueprint\Exceptions\CommuneRuntimeException;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * psr16 的异常.
 */
class Psr16InvalidArgsException
    extends CommuneRuntimeException
    implements InvalidArgumentException
{
}