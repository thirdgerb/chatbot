<?php

/**
 * Class RegexMatcher
 * @package Commune\Chatbot\Framework\Intent
 */

namespace Commune\Chatbot\Framework\Intent\Matcher;


use Commune\Chatbot\Framework\Message\Message;

class RegexMatcher extends Matcher
{

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var array
     */
    private $definition;

    /**
     * RegexMatcher constructor.
     * @param string $pattern
     * @param array $definition
     */
    public function __construct(string $pattern, array $definition)
    {
        $this->pattern = $pattern;
        $this->definition = $definition;
    }


    public function match(Message $message): ? array
    {
        $text = $message->getTrimText();
        if ($text == $this->pattern) {
            return [];
        }
        return null;
    }


}