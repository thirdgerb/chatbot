<?php

/**
 * Class Choice
 * @package Commune\Chatbot\Host\Context\Predefined
 */

namespace Commune\Chatbot\Framework\Context\Predefined;


use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Routing\DialogRoute;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Message\Questions\Choose;
use Illuminate\Support\Str;

class Choice extends ContextCfg
{
    const SCOPE = [
        Scope::MESSAGE
    ];

    const DATA = [
        'choice' => '',
        'fulfill' => false,
    ];

    const PROPS = [
        'question' => '',
        'choices' => [],
        'default' => '',
    ];

    public function prepared(Context $context)
    {
        $context->reply(new Choose($context['question'], $context['choices'], $this['default']));
    }

    public function routing(DialogRoute $route)
    {
        $route->fallback()
            ->action(function(Context $context, Intent $intent){
                $text = $intent->getMessage()->getText();

                $choices = $context['choices'];

                $choice = '';

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

                if ($choice) {
                    $context['choice'] = $choice;
                    $context['fulfill'] = true;
                } else {
                    $context->warn('wrong input');
                }
            })->redirectIf(['fulfill' => true])
                ->intended();
    }

}