<?php

/**
 * Class ReplyMessage
 * @package Commune\Chatbot\Host\Conversation
 */

namespace Commune\Chatbot\Framework\Conversation;


use Commune\Chatbot\Framework\Character\Platform;
use Commune\Chatbot\Framework\Character\Recipient;
use Commune\Chatbot\Framework\Character\User;
use Commune\Chatbot\Framework\Message\Message;

class ReplyMessage
{

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $replyToId;

    /**
     * @var string
     */
    protected $chatId;

    /**
     * @var string
     */
    protected $sessionId;

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
    protected $messages = [];

    /**
     * @var \DateTime
     */
    protected $createAt;




    public function getMessages() {
        return $this->messages;
    }
}