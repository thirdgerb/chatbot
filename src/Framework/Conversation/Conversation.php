<?php

/**
 * Class Request
 * @package Commune\Chatbot\Host\Conversation
 */

namespace Commune\Chatbot\Framework\Conversation;


use Commune\Chatbot\Framework\Character\Platform;
use Commune\Chatbot\Framework\Character\Recipient;
use Commune\Chatbot\Framework\Character\User;
use Commune\Chatbot\Framework\Intent\IntentData;
use Commune\Chatbot\Framework\Message\Message;

class Conversation
{

    /**
     * @var string
     */
    protected $chatId;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var IncomingMessage
     */
    protected $incomingMessage;

    protected $replies = [];

    protected $possibleIntents = [];

    /**
     * @var IntentData
     */
    protected $matchedIntent;


    /**
     * @var Scope
     */
    protected $scope;

    public function __construct(
        IncomingMessage $message,
        string $chatId,
        string $sessionId = null
    )
    {
        $this->incomingMessage = $message;
        $this->chatId = $chatId;
        $this->sessionId = $sessionId;
    }


    /*-------- setter ----------*/


    /**
     * @param string $sessionId
     */
    public function setSessionId(string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }


    /*-------- getter ----------*/

    /**
     * @return IncomingMessage
     */
    public function getIncomingMessage(): IncomingMessage
    {
        return $this->incomingMessage;
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->incomingMessage->getId();
    }

    /**
     * @return string
     */
    public function getChatId() : string
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
     * @return User
     */
    public function getSender(): User
    {
        return $this->incomingMessage->getSender();
    }

    /**
     * @return Recipient
     */
    public function getRecipient(): Recipient
    {
        return $this->incomingMessage->getRecipient();
    }

    /**
     * @return Platform
     */
    public function getPlatform(): Platform
    {
        return $this->incomingMessage->getPlatform();
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->incomingMessage->getMessage();
    }

    /**
     * @return \DateTime
     */
    public function getCreateAt(): \DateTime
    {
        return $this->incomingMessage->getCreateAt();
    }


    /*-------- scope ----------*/

    public function getScope() : Scope
    {
        if (!isset($this->scope)) {
            $this->scope = new Scope(
                $this->getId(),
                $this->getSender()->getId(),
                $this->getRecipient()->getId(),
                $this->getPlatform()->getId(),
                $this->getChatId(),
                $this->getSessionId(),
                $this->getCreateAt()
            );
        }
        return $this->scope;
    }

    /*-------- reply ----------*/

    public function reply(Message $message) {
        $this->replies[] = $message;
    }

    public function getReplies() : array
    {
        return $this->replies;
    }

    public function mergeReplies(Conversation $conversation)
    {
        $this->replies = array_merge($this->replies, $conversation->getReplies());
    }

    /*-------- intent ----------*/


    public function getPossibleIntents() : array
    {
        return $this->possibleIntents;
    }

    /**
     * @return IntentData
     */
    public function getMatchedIntent(): ? IntentData
    {
        if (isset($this->matchedIntent)) {
            return $this->matchedIntent;
        }

        return $this->defaultIntent();
    }

    public function defaultIntent() : IntentData
    {
        return new IntentData($this->getMessage());
    }

    /**
     * @param IntentData $matchedIntent
     */
    public function setMatchedIntent(IntentData $matchedIntent): void
    {
        $this->matchedIntent = $matchedIntent;
    }


    /*-------- command ----------*/

    protected $isCloseSession = false;

    public function closeSession()
    {
        $this->isCloseSession = true;
    }

    public function isCloseSession() : bool
    {
        return $this->isCloseSession;
    }


}