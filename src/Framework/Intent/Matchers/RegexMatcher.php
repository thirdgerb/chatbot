<?php

/**
 * Class RegextMatcher
 * @package Commune\Chatbot\Framework\Intent\Matchers
 */

namespace Commune\Chatbot\Framework\Intent\Matchers;


use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Intent\IntentFactory;

class RegexMatcher implements Matcher
{
    protected $regex;

    protected $fields = [];

    protected $factory;

    public function __construct(IntentFactory $factory, array $defined)
    {
        $this->factory = $factory;
        $this->regex = array_shift($defined);
        $this->fields = $defined;
    }

    public function match(Conversation $conversation): ? Intent
    {
        $text = $conversation->getTrimText();

        if ($text != $this->regex) {
            return null;
        }

        //todo not fulfill
        return $this->factory->makeIntent($conversation);
    }


}