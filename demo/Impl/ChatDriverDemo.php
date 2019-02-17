<?php

/**
 * Class ChatDriverDemo
 * @package Commune\Chatbot\Demo\Impl
 */

namespace Commune\Chatbot\Demo\Impl;


use Commune\Chatbot\Contracts\ChatDriver;
use Commune\Chatbot\Contracts\IdGenerator;
use Commune\Chatbot\Framework\Exceptions\ChatbotException;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Conversation\IncomingMessage;
use Commune\Chatbot\Framework\Message\Message;
use Commune\Chatbot\Framework\Message\Text;

class ChatDriverDemo implements ChatDriver
{
    

    protected $sessionIds = [];

    protected $incomingMessages = [];

    protected $replies = [];

    protected $idGenerator;

    public function __construct(IdGenerator $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    /**
     * @param string $chatId
     * @return string
     * @throws \Exception
     */
    public function fetchSessionIdOfChat(string $chatId): string
    {
        if (!isset($this->sessionIds[$chatId])) {
            $this->sessionIds[$chatId] = $this->idGenerator->makeSessionId($chatId);
        }
        return $this->sessionIds[$chatId];
    }

    public function fetchIdOrCreateChat(Conversation $conversation): string
    {
        return $conversation->getChatId();
    }


    public function closeSession(string $chatId)
    {
        unset($this->sessionIds[$chatId]);
    }


    public function chatIsTooBusy(string $chatId): bool
    {
        return false;
    }

    public function lockChat(string $chatId): bool
    {
        return true;
    }

    public function unlockChat(string $chatId)
    {
    }

    public function pushIncomingMessage(string $chatId, string $sessionId, IncomingMessage $message)
    {
        $array = $this->incomingMessages[$chatId] ?? [];
        $array[] = $message;
        $this->incomingMessages[$chatId] = $array;
    }

    public function popIncomingMessage(string $chatId): ? IncomingMessage
    {
        $array = $this->incomingMessages[$chatId] ?? [];
        $message = array_pop($array);
        $this->incomingMessages[$chatId] = $array;
        return $message;
    }

    public function saveReplies(Conversation $conversation)
    {
    }

    public function flushAwaitIncomingMessages(string $chatId)
    {
    }

    public function replyWhenTooBusy(): Message
    {
        return new Text('too busy');
    }

    public function replyWhenException(ChatbotException $e): Message
    {
        return new Text(strval($e));
    }

}