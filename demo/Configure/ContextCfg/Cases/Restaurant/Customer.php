<?php

/**
 * Class YourName
 * @package Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Restaurant
 */

namespace Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Restaurant;


use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Context\Predefined\Answer;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Routing\DialogRoute;

class Customer extends ContextCfg
{
    const SCOPE = [Scope::SENDER];

    const DEPENDS = [
        'name' => [
            Answer::class,
            [
                'question' => '请问您怎么称呼?',
                'default' => 'x'
            ]
        ]
    ];

    const MUTATOR = [
        'call'
    ];

    const DATA = [
        'times' => 1,
    ];

    public function routing(DialogRoute $route)
    {
        $route->prepared()
            ->action()
                ->call(function(Context $context, Intent $intent){
                    $context->info($context['call'] .', 您是第' . $context['times'] .'光临本店');
                })
            ->redirect()
                ->intended();
    }


    public function getCall(Context $context)
    {
        return $context['name'] . '先生';
    }


}