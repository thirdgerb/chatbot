<?php

/**
 * Class ChatbotPipe
 * @package Commune\Chatbot\Framework\Pipeline
 */

namespace Commune\Chatbot\Framework\Pipeline;

use Carbon\Carbon;
use Commune\Chatbot\Blueprint\Pipeline\ChatbotPipe as Blueprint;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Framework\Exceptions\ConversationalException;

use Commune\Chatbot\Framework\Exceptions\LogicException;
use Commune\Chatbot\Framework\Exceptions\PipelineException;
use Commune\Chatbot\Framework\Exceptions\RuntimeException;

/**
 * Class ChatbotPipe
 * @package Commune\Chatbot\Framework\Pipeline
 */
abstract class ChatbotPipeImpl implements Blueprint
{
    use PipelineLog;

    public function getPipeName(): string
    {
        return static::class;
    }

    abstract public function handleUserMessage(Conversation $conversation, \Closure $next) : Conversation;

    abstract public function onUserMessageFinally(Conversation $conversation) : void;



    public function handle(Conversation $conversation, \Closure $next) : Conversation
    {
        try {

            $start = CHATBOT_DEBUG ? new Carbon() : null;
            $this->startPipe($conversation);

            // 真正运行逻辑
            $result = $this->handleUserMessage($conversation, $next);

            $this->endPipe(
                $conversation,
                $start,
                $end = CHATBOT_DEBUG ? new Carbon() : null
            );

            return $result;

        // 直接中断流程的异常, 携带conversation, 可以直接中断后续的逻辑.
        // 但还是要执行 finally
        } catch (ConversationalException $e) {
            return $e->getConversation();

        } finally {
            $this->onUserMessageFinally($conversation);

        }
    }

}