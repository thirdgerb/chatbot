<?php

/**
 * Class IncomingMessage
 * @package Commune\Chatbot\Host\Conversation
 */

namespace Commune\Chatbot\Framework\Conversation;

use Commune\Chatbot\Framework\Character\Platform;
use Commune\Chatbot\Framework\Character\Recipient;
use Commune\Chatbot\Framework\Character\User;
use Commune\Chatbot\Framework\Message\Message;

class IncomingMessage
{

    /**
     * @var string
     */
    protected $id;

    /**
     * @var User
     */
    protected $sender;

    /**
     * @var Recipient
     */
    protected $recipient;

    /**
     * @var Platform
     */
    protected $platform;

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var \DateTime
     */
    protected $createAt;

    /**
     * IncomingMessage constructor.
     * @param string $id
     * @param User $sender
     * @param Recipient $recipient
     * @param Platform $platform
     * @param Message $message
     * @param \DateTime $createAt
     */
    public function __construct(string $id, User $sender, Recipient $recipient, Platform $platform, Message $message, \DateTime $createAt)
    {
        $this->id = $id;
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->platform = $platform;
        $this->message = $message;
        $this->createAt = $createAt;
    }


        /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getSender(): User
    {
        return $this->sender;
    }

    /**
     * @return Recipient
     */
    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }

    /**
     * @return Platform
     */
    public function getPlatform(): Platform
    {
        return $this->platform;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /**
     * @return \DateTime
     */
    public function getCreateAt(): \DateTime
    {
        return $this->createAt;
    }

}