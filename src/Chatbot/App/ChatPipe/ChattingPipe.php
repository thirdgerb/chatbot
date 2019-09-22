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
        $chatId = $chat->getChatId();

        // 锁chat 失败
        $locked = $this->lockChat($chatId);

        // 没锁到就直接返回好了.
        if (! $locked) {
            $conversation->getSpeech()->warning(
                $this->config
                    ->defaultMessages
                    ->chatIsTooBusy
            );
            return $conversation;
        }

        /**
         * @var Conversation $replyConversation
         */
        $replyConversation = $next($conversation);

        $this->unlockChat($chatId);
        return $replyConversation;

    }

    /*----------- finally -----------*/

    public function onUserMessageFinally(Conversation $conversation): void
    {
        $this->unlockChat($conversation->getChat()->getChatId());
    }


    /*----------- chat 管理 -----------*/

    public function lockChat(string $chatId) : bool
    {
        return $this->cache->lock($this->getChatLockerKey($chatId), 2);
    }

    public function unlockChat(string $chatId) : void
    {
        $this->cache->forget($this->getChatLockerKey($chatId));
    }

    public function getChatLockerKey(string $chatId) : string
    {
        return "chatbot:chatLocker:" . $chatId;
    }

}