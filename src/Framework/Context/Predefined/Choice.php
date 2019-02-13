<?php

/**
 * Class Choice
 * @package Commune\Chatbot\Host\Context\Predefined
 */

namespace Commune\Chatbot\Framework\Context\Predefined;


use Commune\Chatbot\Framework\Constants\Lang;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Routing\DialogRoute;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Message\Questions\Choose;
use Commune\Chatbot\Framework\Support\TypeTransfer;
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
            ->action(function(Context $context, Intent $intent){
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
                    $context['choice'] = $choice;
                    $context['fulfill'] = true;
                } else {
                    $context->warn(Lang::WRONG_INPUT);
                    $this->prepared($context);
                }
            })->redirectIf(['fulfill' => true])
                ->intended();
    }

    public function toString(Context $context) : string
    {
        return TypeTransfer::toString($context['choice']);
    }


}