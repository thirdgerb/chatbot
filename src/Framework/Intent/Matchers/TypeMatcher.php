<?php

/**
 * Class TypeMatche
 * @package Commune\Chatbot\Framework\Intent\Matchers
 */

namespace Commune\Chatbot\Framework\Intent\Matchers;


use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Intent\Predefined\TypeIntent;

class TypeMatcher implements Matcher
{
    /**
     * @var string
     */
    protected $messageType;

    /**
     * TypeMatcher constructor.
     * @param string $messageType
     */
    public function __construct(string $messageType)
    {
        $this->messageType = $messageType;
    }

    public function match(Conversation $conversation): ? Intent
    {
        if ($conversation->getMessageType() === $this->messageType) {
            return new TypeIntent($conversation->getMessage());
        }
        return null;
    }


}