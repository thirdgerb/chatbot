<?php

/**
 * Class TooBusyException
 * @package Commune\Chatbot\Framework\ChatbotPipe\Messenger\Exceptions
 */

namespace Commune\Chatbot\App\ChatPipe\Chatting;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Framework\Exceptions\LogicException;

/**
 * Class ChatTooBusyException
 * @package Commune\Chatbot\Framework\ChatbotPipe\Messenger\Exceptions
 */
class ChatTooBusyException extends LogicException
{
    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * TooBusyException constructor.
     * @param Conversation $conversation
     */
    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
        parent::__construct();
    }


}