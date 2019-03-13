<?php

/**
 * Class Welcome
 * @package Commune\Chatbot\Demo\Configure\ContextCfg\Cases
 */

namespace Commune\Chatbot\Demo\Configure\ContextCfg\Cases;


use Commune\Chatbot\Demo\Configure\ContextCfg\Cases\ApiCases\ApiRoot;
use Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Restaurant\Waiter;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Message\Questions\Choose;
use Commune\Chatbot\Framework\Routing\DialogRoute;

class Welcome extends ContextCfg
{
    const SCOPE = [Scope::SESSION];


    const DATA = [
        'visited' => false,
        'times' => 0,
    ];


    public function prepared(Context $context)
    {
        $context['times'] += 1 ;

        if ($context['visited']) {
            $context->info("欢迎回到正规测试用例. 您是第" . $context['times'] . '次到来.');
        } else {
            $context['visited'] = true;
            $context->info("欢迎来到正规测试用例! 您是第" . $context['times'] . '次到来.');
        }

        $context->info('在当前语境下可以选择想尝试的测试用例.');

        $this->welcome($context);
    }

    public function welcome(Context $context, Intent $intent = null)
    {
        $context->reply(new Choose(
            '请选择您想测试的用例',
            [
                1 => '点餐用例: 会模拟一个点餐的场景, 测试流程',
                2 => 'API用例: 会模拟调用api',
                3 => '自我介绍: 介绍chatbot功能同时, 验证上下文记忆, 语境跳转等功能',
                4 => '功能测试用例: 测试系统的feature',
            ],
            1
        ));
    }

    public function routing(DialogRoute $route)
    {
        $route->fallback()
            ->action()
                ->info("不好意思, 无法理解您的意图. 请选择 1 ~ 4, 默认为 1")
                ->info("===================")
                ->callSelf('welcome');


        $route->hearsCommand('order')
            ->exactly('')
            ->exactly(1)
            ->exactly('点餐')
            ->redirect()
                ->to(Waiter::class);

        $route->hears(2)
            ->action()
                ->info("选择了2, 进入api用例")
            ->redirect()
                ->to(ApiRoot::class);
    }


}