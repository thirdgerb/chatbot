<?php

/**
 * Class ExceptionHandler
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;

use Commune\Chatbot\App\ChatPipe\MessengerPipe;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Framework\Exceptions\FatalExceptionHandler;

/**
 * 处理无法正常用消息来响应的异常.
 *
 * @see MessengerPipe
 * @see FatalExceptionHandler
 */
interface ExceptionHandler
{

    /**
     * 处理无法响应的异常.
     *
     * @param Conversation $conversation
     * @param \Throwable $e
     * @return Conversation
     */
    public function handleException(Conversation $conversation, \Throwable $e) : Conversation;

    /**
     * 记录异常. 比如发送给 sentry
     *
     * @param Conversation $conversation
     * @param \Throwable $e
     */
    public function reportException(
        Conversation $conversation,
        \Throwable $e
    ) : void;

}