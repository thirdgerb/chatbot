<?php

namespace Commune\Chatbot\Framework\Exceptions;

use Throwable;

/**
 * 系统的逻辑导致的异常.
 * 如果在会话内发生, 会终止会话.
 * 会话外发生, 会终止系统运行, 不应该存在.
 */
class ChatbotLogicException extends \LogicException
{
    public function __construct(string $message = "",  Throwable $previous = null)
    {
        parent::__construct($message, 255, $previous);
    }
}