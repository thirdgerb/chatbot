<?php

/**
 * Class Confirmation
 * @package Commune\Chatbot\Host\Context\Predefined
 */

namespace Commune\Chatbot\Framework\Context\Predefined;


use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Routing\DialogRoute;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Message\Questions\Confirm;
use Illuminate\Support\Str;

class Confirmation extends Question
{
    const PROPS = [
        'question' => '',
        'default' => 'yes'
    ];

    public function prepared(Context $context)
    {
        $context->reply(new Confirm($context['question'], $context['default']));
    }


    public function routing(DialogRoute $route)
    {
        $route->fallback()
            ->action()
            ->callSelf('fallback')
                ->redirect()
                ->intended();
    }

    public function fallback(Context $context, Intent $intent)
    {
        $text = $intent->getMessage()->getTrimText();
        $default = $context['default'];
        $context['result'] = $result = $text === '' || Str::startsWith($default, $text);
    }


}