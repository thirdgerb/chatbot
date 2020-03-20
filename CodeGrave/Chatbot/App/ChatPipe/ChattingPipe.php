<?php

namespace Commune\Chatbot\App\ChatPipe;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Framework\Pipeline\ChatbotPipeImpl;

class ChattingPipe extends ChatbotPipeImpl
{

    /**
     * @var CacheAdapter
     */
    public $cache;

    /**
     * @var ChatbotConfig
     */
    public $config;


    public function __construct(
        CacheAdapter $cache,
        ChatbotConfig $config
    )
    {
        $this->cache = $cache;
        $this->config = $config;
    }


    /*----------- on user message -----------*/


    public function handleUserMessage(Conversation $conversation, \Closure $next): Conversation
    {
        $chat = $conversation->getChat();
        $ttl = $conversation->getChatbotConfig()->chatLockerExpire;

        // 锁chat
        $locked = $chat->lock($ttl);

        // 没锁到就直接返回好了.
        if (! $locked) {
            $conversation->getSpeech()->warning(
                $this->config
                    ->defaultMessages
                    ->chatIsTooBusy
            );
            $conversation->getLogger()->warning(__METHOD__ . ' chat is too busy');
            return $conversation;
        }

        /**
         * @var Conversation $replyConversation
         */
        $replyConversation = $next($conversation);
        return $replyConversation;
    }

    /*----------- finally -----------*/

    public function onFinally(Conversation $conversation): void
    {
        // 出错后主动解锁.
        $conversation->getChat()->unlock();
    }


}