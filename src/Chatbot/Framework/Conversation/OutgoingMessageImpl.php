<?php

/**
 * Class OutgoingMessage
 * @package Commune\Chatbot\Framework\Conversation
 */

namespace Commune\Chatbot\Framework\Conversation;


use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\Blueprint\Message\Message;

class OutgoingMessageImpl extends ConversationMessageImpl
{
    public function __construct(
        IncomingMessage $incomingMessage,
        string $messageId,
        Message $reply
    )
    {
        parent::__construct(
            $messageId,
            $reply,
            $incomingMessage->getUserId(),
            $incomingMessage->getChatbotUserId(),
            $incomingMessage->getPlatformId(),
            $incomingMessage->getChatId(),
            $incomingMessage->getId(),
            $incomingMessage->getMessage(),
            $incomingMessage->getTraceId()
        );
    }
}