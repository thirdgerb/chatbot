<?php

/**
 * Class IncomingUserMessage
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;


use Carbon\Carbon;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Conversation\IncomingMessageImpl;

/**
 * ChatbotApp 运行 kernel 的 RequestHandler 之一.
 * 用于处理 UserMessage
 *
 * Class AbsUserMessageHandler
 * @package Commune\Chatbot\Contracts
 */
abstract class AbsUserRequestHandler implements UserMessageRequest
{

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * @var IncomingMessageImpl
     */
    protected $incomingMessage;

    /**
     * @var Input
     */
    protected $input;

    /**
     * @var Output
     */
    protected $output;

    /**
     * UserMessageAdapter constructor.
     * @param Conversation $conversation
     */
    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    public function fetchChatId(): string
    {
        if (isset($this->chatId)) {
            return $this->chatId;
        }
        $platform = $this->getPlatformId();
        $sender = $this->fetchSenderId();
        $recipient = $this->getChatbotUserId();

        $this->chatId = md5("chatbot_chatId:$platform&&$sender&&$recipient");
        return $this->chatId;
    }

    abstract public function generateMessageId(): string;

    abstract public function getChatbotUserId(): string;

    public function getPlatformId(): string
    {
        return static::class;
    }

    abstract public function fetchMessage(): Message;

    abstract public function fetchMessageId(): string;

    abstract public function fetchSenderId(): string;

    abstract public function isDuplex(): bool;


    abstract public function bufferMessageToChat(ConversationMessage $message): void;


    abstract public function flushChatMessages(): void;



    public function getLocale(): ? string
    {
        return null;
    }

    /**
     * @return Input
     */
    public function getInput()
    {
        return $this->input ??
            ( $this->input = $this->conversation->getInput() );
    }

    public function getOutput()
    {
        return $this->output ??
            ( $this->output = $this->conversation->getOutput() );
    }

    public function getCreatedAt(): Carbon
    {
        return $this->fetchMessage()->getCreatedAt();
    }


    public function finishRequest() : void
    {
        // 真正发送的逻辑. 如果之前flush过, 现在就没货了.
        $this->flushChatMessages();
    }


    public function fetchTraceId(): string
    {
        // 默认都用incoming message id.
        // 除非 request 里可以拿到一个独立的trace id
        return $this->fetchMessageId();
    }


}