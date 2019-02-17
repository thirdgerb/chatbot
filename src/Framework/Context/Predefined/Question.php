<?php

/**
 * Class Question
 * @package Commune\Chatbot\Framework\Context\Predefined
 */

namespace Commune\Chatbot\Framework\Context\Predefined;


use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Support\ChatbotUtils;

abstract class Question extends ContextCfg
{
    const SCOPE = [
        Scope::MESSAGE
    ];


    const DATA = [
        'result' => '',
    ];


    public function toString(Context $context) : string
    {
        return ChatbotUtils::toString($context['result']);
    }

    abstract public function fallback(Context $context, Intent $intent);
}