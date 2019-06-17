<?php

/**
 * Class RuntimeExceptionInterface
 * @package Commune\Chatbot\Framework\Exceptions
 */

namespace Commune\Chatbot\Framework\Exceptions;

use Commune\Chatbot\Blueprint\Exceptions\RuntimeExceptionInterface;

/**
 * 系统中不需要捕获的运行环境异常.
 * 理论上应该让 chatbot 进程退出.
 *
 * Interface RuntimeExceptionInterface
 * @package Commune\Chatbot\Framework\Exceptions
 */
class RuntimeException extends \RuntimeException implements RuntimeExceptionInterface
{

    /**
     * RuntimeException constructor.
     * @param string $message
     * @param \Throwable $e
     */
    public function __construct(string $message = '', \Throwable $e = null)
    {
        parent::__construct($message, 0, $e);
    }
}