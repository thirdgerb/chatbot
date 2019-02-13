<?php

/**
 * Class Confirmation
 * @package Commune\Chatbot\Host\Context\Predefined
 */

namespace Commune\Chatbot\Framework\Context\Predefined;


use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Routing\DialogRoute;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Message\Questions\Confirm;
use Commune\Chatbot\Framework\Support\TypeTransfer;
use Illuminate\Support\Str;

class Confirmation extends ContextCfg
{
    const SCOPE = [
        Scope::MESSAGE
    ];

    const DATA = [
        'confirmation' => null,
    ];

    const PROPS = [
        'question' => '',
        'default' => ''
    ];

    public function prepared(Context $context)
    {
        $context->reply(new Confirm($context['question'], $context['default']));
    }


    public function routing(DialogRoute $route)
    {
        $route->fallback()
            ->action(function(Context $context, Intent $intent){
                $text = $intent->getMessage()->getTrimText();

                $default = $context['default'];
                $context['confirmation'] = $text === '' || Str::startsWith($default, $text);

            })->intended();
    }

    public function toString(Context $context) : string
    {
        return TypeTransfer::toString($context['confirmation']);
    }


}