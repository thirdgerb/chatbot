<?php


namespace Commune\Chatbot\Framework\Conversation;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ConversationLogger;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Support\Uuid\IdGeneratorHelper;

trait MessageRequestHelper
{
    use IdGeneratorHelper;

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * @var ConversationLogger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $messageId;

    /**
     * @var Message|null
     */
    protected $inputMessage;

    public function withConversation(Conversation $conversation) : void
    {
        $this->conversation = $conversation;
        $this->logger = $conversation->getLogger();
        $this->onBindConversation();
    }


    abstract protected function onBindConversation();


    public function finish() : void
    {
        $this->conversation = null;
        $this->logger = null;
        $this->inputMessage = null;
    }

    public function generateMessageId(): string
    {
        return $this->createUuId();
    }

    public function fetchNLU() : ? NLU
    {
        return null;
    }

    public function fetchSessionId() : ? string
    {
        return null;
    }


    public function getChatbotName(): string
    {
        return $this->conversation->getChatbotConfig()->chatbotName;
    }

    public function fetchMessageId(): string
    {
        return $this->messageId ?? $this->messageId = $this->generateMessageId();
    }



    public function fetchMessage(): Message
    {
        if (isset($this->inputMessage)) {
            return $this->inputMessage;
        }

        $input = $this->getInput();
        if ($input instanceof Message) {
            return $this->inputMessage = $input;
        }

        return $this->inputMessage = $this->makeInputMessage($input);

    }

    abstract protected function makeInputMessage($input) : Message;

    public function fetchTraceId(): string
    {
        return $this->fetchMessageId();
    }

    public function fetchChatId(): ? string
    {
        return null;
    }

    public function getLogContext() : array
    {
        return [
            'req' => [
                'scene' => $this->getScene(),
                'traceId' => $this->fetchTraceId(),
                'msgId' => $this->fetchMessageId(),
                'chatId' => $this->fetchChatId(),
                'chatName' => $this->getChatbotName(),
                'sessionId' => $this->fetchSessionId(),
            ],
        ];
    }
}