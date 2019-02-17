<?php

/**
 * Class Choice
 * @package Commune\Chatbot\Host\Context\Predefined
 */

namespace Commune\Chatbot\Framework\Context\Predefined;


use Commune\Chatbot\Framework\Constants\Lang;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Routing\DialogRoute;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Message\Questions\Choose;
use Illuminate\Support\Str;

class Choice extends Question
{
    const PROPS = [
        'question' => '',
        'choices' => [],
        'default' => 0,
    ];

    public function prepared(Context $context)
    {
        $context->reply(new Choose($context['question'], $context['choices'], $this->defaultChoice($context)));
    }

    protected function defaultChoice(Context $context)
    {
        $choices = $context['choices'];
        return $choices[$context['default']] ?? $choices[0];
    }

    public function routing(DialogRoute $route)
    {
        $route->fallback()
            ->action()
            ->callSelf('fallback')->redirect(function(Context $context){
                    return '' != $context['result'];
                })->intended();
    }

    public function fallback(Context $context, Intent $intent)
    {
        $text = $intent->getMessage()->getText();

        $choices = $context['choices'];

        $choice = null;

        if (is_numeric($text) && array_key_exists($text, $choices)) {
            $choice = $choices[$text];
        } elseif(trim($text) === '') {

            foreach($choices as $index => $val) {
                if (Str::startsWith($val, $text)) {
                    $choice = $val;
                    break;
                }
            }
        }

        if (isset($choice)) {
            $context['result'] = $choice;
        } else {
            $context->warn(Lang::WRONG_INPUT);
            $this->prepared($context);
        }
    }


}