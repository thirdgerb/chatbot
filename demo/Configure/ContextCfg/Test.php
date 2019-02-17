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
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Routing\DialogRoute;

class Test extends ContextCfg
{
    const SCOPE = [Scope::MESSAGE];

    const DEPENDS = [
        'q1' => [
            Answer::class,
            [
                'question' => 'test 依赖当前问题的答案, 请输入回答. 输入back会返回上一页',
                'default' => 'back'
            ]
        ],
    ];

    public function prepared(Context $context)
    {
        $context->info('进入test语境. 依赖回答为: '. $context['q1']['result']);
    }

    public function routing(DialogRoute $route)
    {
        $route->prepared()
            ->redirect(function(Context $context) {
                $back = $context['q1']['result'] === 'back';
                if ($back) {
                    $context->info('由于输入为back, 将会返回上一单元');
                }
                return $back;
            })->backward();

        $route->fallback()
            ->action()
            ->call(function(Context $context, Intent $intent){
                $context->info('test:' .$intent->getMessage()->getText());
            }) ;

        $route->hears('hello')
            ->action()
            ->call(function (Context $context, Intent $intent) {
                $context->info("hello world");
            });

        $route->hears('back')
            ->action()
            ->info('go backward')
                ->redirect()
                ->backward();

        $route->hears('test format')
            ->action()
            ->call(function (Context $context, Intent $intent) {
                $context->info($context->format('测试format: {}', ['q1.answer']));
            });
    }



}