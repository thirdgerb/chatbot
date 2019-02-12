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
use Commune\Chatbot\Framework\Intent\IntentData;
use Commune\Chatbot\Framework\Message\Questions\Confirm;
use Illuminate\Support\Str;

class Confirmation extends ContextCfg
{
    const SCOPE = [
        Scope::MESSAGE
    ];

    const DATA = [
        'confirmation' => null,
        'fulfill' => false,
    ];

    const PROPS = [
        'question' => '',
        'default' => true
    ];

    public function prepared(Context $context)
    {
        $context->reply(new Confirm($context['question'], $this['default']));
    }


    public function routing(DialogRoute $route)
    {
        $route->fallback()
            ->action(function(Context $context, IntentData $intent){
                $text = $intent->getMessage()->getText();

                $choices = $context['choices'];


                $choice = null;
                if (is_numeric($text) && array_key_exists($text, $choices)) {

                    $choice = $choices[$text];

                } elseif(trim($text) === '') {

                    $choice = $choices[$context['default']] ?? $choices[0];

                    foreach($choices as $index => $val) {
                        if (Str::startsWith($val, $text)) {
                            $choice = $val;
                            break;
                        }
                    }
                }

                if (isset($choice)) {
                    $context['choice'] = $choice;
                    $context['fulfill'] = true;
                } else {
                    $context->warn('wrong input');
                    $this->prepared($context);
                }
            })->redirectIf(['fulfill' => true])
                ->intended();
    }



}