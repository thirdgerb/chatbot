<?php

namespace Commune\Chatbot\Framework\Exceptions;

/**
 * 系统启动的时候发生的异常, 应该终止启动.
 */
class BootingException extends ChatbotLogicException
{

    public function __construct($message = '', \Throwable $e = null)
    {
        parent::__construct(
            'chatbot booting failure : ' . $message,
            $e
        );
    }
}