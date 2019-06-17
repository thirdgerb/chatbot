<?php

/**
 * Class LogicExceptionInterface
 * @package Commune\Chatbot\Framework\Exceptions
 */

namespace Commune\Chatbot\Framework\Exceptions;

use Throwable;

/**
 * Chatbot 系统内的逻辑异常.
 *
 * @package Commune\Chatbot\Framework\Exceptions
 */
class LogicException extends \LogicException
{

    public function __construct(string $message = "", Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}