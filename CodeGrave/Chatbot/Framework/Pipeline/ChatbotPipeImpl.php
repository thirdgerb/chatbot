<?php


namespace Commune\Chatbot\Framework\Pipeline;

use Carbon\Carbon;
use Commune\Chatbot\Blueprint\Pipeline\ChatbotPipe as Blueprint;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Exceptions\ChatbotRuntimeException;
use Commune\Chatbot\Blueprint\Exceptions\ConversationalException;

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

    /**
     * 任何时候都会执行的逻辑.
     * @param Conversation $conversation
     */
    abstract public function onFinally(Conversation $conversation) : void;


    /**
     * @param Conversation $conversation
     * @param \Closure $next
     * @return Conversation
     * @throws \Throwable
     */
    public function handle(Conversation $conversation, \Closure $next) : Conversation
    {
        try {

            $start = CHATBOT_DEBUG ? new Carbon() : null;

            // 真正运行逻辑
            $result = $this->handleUserMessage($conversation, $next);

            $this->endPipe(
                $conversation,
                $start,
                $end = CHATBOT_DEBUG ? new Carbon() : null
            );

            return $result;

        // 系统的异常, 透传. 和其它的 Runtime Exception 相区别
        } catch (ChatbotRuntimeException $e) {
            throw $e;

        // 偶发的异常, 不影响对话继续.
        } catch (\RuntimeException $e) {
            throw new ConversationalException($this->getPipeName() . ' catch runtime exception', $e);

        } finally {
            $this->onFinally($conversation);
        }
    }

}