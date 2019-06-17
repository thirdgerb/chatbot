<?php

/**
 * Class FatalErrorException
 * @package Commune\Chatbot\Framework\Exceptions
 */

namespace Commune\Chatbot\Framework\Exceptions;


use Commune\Chatbot\Blueprint\Exceptions\StopServiceExceptionInterface;

/**
 * 系统启动的时候发生的异常, 应该终止启动.
 */
class BootingException extends \RuntimeException implements StopServiceExceptionInterface
{

    public function __construct(\Throwable $e)
    {
        parent::__construct(
            'chatbot booting failure : ' . $e->getMessage(),
            255,
            $e
        );
    }
}