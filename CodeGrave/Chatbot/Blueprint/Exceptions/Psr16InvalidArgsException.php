<?php


namespace Commune\Chatbot\Blueprint\Exceptions;


use Psr\SimpleCache\InvalidArgumentException;

/**
 * psr16 的异常.
 */
class Psr16InvalidArgsException extends \LogicException implements InvalidArgumentException
{
}