<?php

/**
 * Class Answer
 * @package Commune\Chatbot\Host\Context\Predefined
 */

namespace Commune\Chatbot\Framework\Context\Predefined;


use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Routing\DialogRoute;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Message\Questions\Ask;

class Answer extends Question
{
    const PROPS = [
        'question' => '',
        'default' => ''
    ];

    public function prepared(Context $context)
    {
        $context->reply(new Ask($context['question'], $context['default']));
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
        $text = $intent->getMessage()->getText();
        $text = !empty($text) ? $text : $context['default'];
        $context['result'] = $text;
    }


}