<?php

/**
 * Class Test
 * @package Commune\Chatbot\Demo\Configure\ContextCfg
 */

namespace Commune\Chatbot\Demo\Configure\ContextCfg;

use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Context\Predefined\Answer;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Intent\IntentData;
use Commune\Chatbot\Framework\Routing\DialogRoute;

class Test extends ContextCfg
{
    const SCOPE = [Scope::MESSAGE];

    const DEPENDS = [
        'q1' => [
            Answer::class,
            [
                'question' => '请输入q1做测试',
                'default' => 'q1'
            ]
        ],
    ];

    public function prepared(Context $context)
    {
        $context->info('now test, answer is :'. $context['q1']['answer']);
    }

    public function routing(DialogRoute $route)
    {
        $route->prepared()
            ->redirectIf(function(Context $context) {
                return $context['q1']['answer'] === 'back';
            })->backward();

        $route->fallback()
            ->action(function(Context $context, IntentData $intent){
                $context->info('test:' .$intent->getMessage()->getText());
            }) ;

        $route->hearsRegex('hello')
            ->action(function (Context $context, IntentData $intent) {
                $context->info("hello world");
            });

        $route->hearsRegex('back')
            ->info('go backward')
            ->backward();

    }

    public function created(Context $context)
    {
        $context->info(static::class. '::initial');
    }

    public function waked(Context $context)
    {
        $context->info(static::class. '::wake');
    }

}