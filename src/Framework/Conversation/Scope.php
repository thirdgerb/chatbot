<?php

/**
 * Class Scope
 * @package Commune\Chatbot\Host\Conversation
 */

namespace Commune\Chatbot\Framework\Conversation;

use DateTime;

class Scope
{
    const MESSAGE = 0;
    const SENDER = 1;
    const RECIPIENT = 2;
    const PLATFORM = 3;
    const CHAT = 4;
    const SESSION = 5;


    /**
     * @var string
     */
    protected $messageId;

    /**
     * @var string
     */
    protected $senderId;

    /**
     * @var string
     */
    protected $recipientId;

    /**
     * @var string
     */
    protected $platformId;
    /**
     * @var string
     */
    protected $chatId;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Scope constructor.
     * @param string $messageId
     * @param string $senderId
     * @param string $recipientId
     * @param string $platformId
     * @param string $chatId
     * @param string $sessionId
     * @param DateTime $timestamp
     */
    public function __construct(
        string $messageId,
        string $senderId,
        string $recipientId,
        string $platformId,
        string $chatId,
        string $sessionId,
        DateTime $timestamp
    )
    {
        $this->messageId = $messageId;
        $this->senderId = $senderId;
        $this->recipientId = $recipientId;
        $this->platformId = $platformId;
        $this->chatId = $chatId;
        $this->sessionId = $sessionId;
        $this->createdAt = $timestamp;
    }

    public static function getScopeName(int $scopeType)
    {
        switch($scopeType) {
            case self::MESSAGE:
                return 'messageId';
            case self::SENDER:
                return 'senderId';
            case self::RECIPIENT:
                return 'recipientId';
            case self::PLATFORM:
                return 'platformId';
            case self::CHAT:
                return 'chatId';
            case self::SESSION;
                return 'sessionId';
            default :
                return 'unknown-'.$scopeType;
        }
    }

    /**
     * @return string
     */
    public function getPlatformId(): string
    {
        return $this->platformId;
    }


    /**
     * @return string
     */
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * @return string
     */
    public function getSenderId(): string
    {
        return $this->senderId;
    }

    /**
     * @return string
     */
    public function getRecipientId(): string
    {
        return $this->recipientId;
    }

    /**
     * @return string
     */
    public function getChatId(): string
    {
        return $this->chatId;
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt() : DateTime
    {
        return $this->createdAt;
    }

    public function getScope(int $scopeEnum) : string
    {
        switch($scopeEnum) {
            case self::MESSAGE :
                return $this->getMessageId();
            case self::SENDER :
                return $this->getSenderId();
            case self::RECIPIENT :
                return $this->getRecipientId();
            case self::PLATFORM :
                return $this->getPlatformId();
            case self::CHAT :
                return $this->getChatId();
            case self::SESSION :
                return $this->getSessionId();
            default :
                return '';
        }
    }

    public function makeDialogId(string $dialogName, array $scope) : string
    {
        sort($scope);
        $idStr = $dialogName;
        foreach($scope as $type) {
            $idStr .= ":$type:".$this->getScope($type);
        }
        return md5($idStr);
    }

    public function toArray()
    {
        return [
            self::MESSAGE => $this->getMessageId(),
            self::SENDER => $this->getSenderId(),
            self::RECIPIENT => $this->getRecipientId(),
            self::PLATFORM => $this->getPlatformId(),
            self::CHAT => $this->getChatId(),
            self::SESSION => $this->getSenderId(),
        ];
    }

    public function toMap()  : array
    {
        $result = [];
        foreach($this->toArray() as $index => $val) {
            $result[static::getScopeName($index)] = $val;
        }

        return $result;
    }

}