<?php


namespace Commune\Chatbot\Framework\Conversation;


use Carbon\Carbon;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Support\Arr\ArrayAbleToJson;


/**
 * Class ConversationMessageImpl
 * @package Commune\Chatbot\Framework\Conversation
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $id
 * @property-read string $chatId
 * @property-read string $userId
 * @property-read string $traceId
 * @property-read string $platformId
 * @property-read Carbon $createdAt
 * @property-read Message $message
 * @property-read string|null $replyToId
 */
class ConversationMessageImpl implements ConversationMessage
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    protected $messageId;

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var string
     */
    protected $chatId;

    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $chatbotUserId;

    /**
     * @var string
     */
    protected $platformId;

    /**
     * @var Carbon
     */
    protected $createdAt;

    /**
     * @var string | null
     */
    protected $replyToId;

    /**
     * @var null|Message
     */
    protected $replyToMessage;

    /**
     * @var string|null
     */
    protected $traceId;

    public function __construct(
        string $messageId,
        Message $message,
        string $userId,
        string $chatbotUserId,
        string $platformId,
        string $chatId,
        string $replyToId = null,
        string $traceId = null
    )
    {
        $this->messageId = $messageId;
        $this->message = $message;
        $this->userId = $userId;
        $this->chatbotUserId = $chatbotUserId;
        $this->chatId = $chatId;

        $this->replyToId = $replyToId;
        $this->platformId = $platformId;

        $this->createdAt = $message->getCreatedAt();
        $this->traceId = $traceId;
    }


    public function getTraceId(): string
    {
        return $this->traceId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->messageId;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getChatbotUserId(): string
    {
        return $this->chatbotUserId;
    }


    /**
     * @return string
     */
    public function getPlatformId(): string
    {
        return $this->platformId;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function getReplyToId(): ? string
    {
        return $this->replyToId;
    }

    public function toArray() : array
    {
        return [
            'messageId' => $this->messageId,
            'message' => $this->message,
            'replyTo' => $this->replyToId,
            'replyToMessage' => $this->replyToMessage,
            'chatId' => $this->chatId,
            'platformId' => $this->platformId,
            'userId' => $this->userId,
            'chatbotUserId' => $this->chatbotUserId,
            'createAt' => $this->getCreatedAt()->toDateTimeString(),
            'traceId' => $this->getTraceId(),
        ];
    }

    public function __get($name)
    {
        return $this->{$name};
    }
}