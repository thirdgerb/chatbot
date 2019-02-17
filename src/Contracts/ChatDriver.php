<?php

/**
 * Class ChatDriver
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;


use Commune\Chatbot\Framework\Exceptions\ChatbotException;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Conversation\IncomingMessage;
use Commune\Chatbot\Framework\Message\Message;

interface ChatDriver
{
    public function fetchIdOrCreateChat(Conversation $conversation) : string;

    public function fetchSessionIdOfChat(string $chatId) : string;

    public function closeSession(string $chatId);

    /*--------- 状态 ----------*/

    public function chatIsTooBusy(string $chatId) : bool;

    public function lockChat(string $chatId) : bool;

    public function unlockChat(string $chatId);


    /*--------- 消息管理 ----------*/

    public function pushIncomingMessage(string $chatId, string $sessionId, IncomingMessage $message);

    public function popIncomingMessage(string $chatId) : ? IncomingMessage;

    public function saveReplies(Conversation $conversation);



    /*--------- 异常处理 ----------*/

    public function flushAwaitIncomingMessages(string $chatId);

    public function replyWhenTooBusy() : Message;

    public function replyWhenException(ChatbotException $e) : Message;

}