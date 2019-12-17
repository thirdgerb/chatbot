<?php


namespace Commune\Chatbot\Framework\Exceptions;

use Throwable;


/**
 * 对话机器人的运行中异常.
 */
class ChatbotRuntimeException extends \RuntimeException
{
    public function __construct(string $message = "", Throwable $previous = null)
    {
        if (isset($previous)) {
            $message .= '; next: '
                . get_class($previous)
                . ': '
                . $previous->getMessage();
        }
        parent::__construct($message, 254, $previous);
    }
}