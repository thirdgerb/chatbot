<?php


namespace Commune\Chatbot\Framework\Conversation;
use Commune\Chatbot\Blueprint\Conversation\Chat;
use Commune\Chatbot\Blueprint\Message\Message;


/**
 * 发送给其他用户的特殊消息.
 * 要用conversation bufferConversationMessage
 */
class ToChatMessage extends ConversationMessageImpl
{

    public function __construct(
        Chat $chat,
        string $messageId,
        Message $message,
        string $traceId = null,
        string $replyToId = null,
        Message $replyToMessage = null
    )
    {
        parent::__construct(
            $messageId,
            $message,
            $chat->getUserId(),
            $chat->getChatbotUserId(),
            $chat->getPlatformId(),
            $chat->getChatId(),
            $replyToId,
            $replyToMessage,
            $traceId
        );
    }


}