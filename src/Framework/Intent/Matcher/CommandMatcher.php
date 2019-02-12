<?php

/**
 * Class Command
 * @package Commune\Chatbot\Framework\Intent
 */

namespace Commune\Chatbot\Framework\Intent\Matcher;


use Commune\Chatbot\Framework\Message\Message;

class CommandMatcher extends Matcher
{

    /**
     * @var string
     */
    private $signature;

    /**
     * CommandMatcher constructor.
     * @param string $signature
     */
    public function __construct(string $signature)
    {
        $this->signature = $signature;
    }


    public function match(Message $message): ? array
    {
        // TODO: Implement match() method.
    }

}