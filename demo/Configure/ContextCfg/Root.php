<?php

/**
 * Class Root
 * @package Commune\Chatbot\Demo\Configure\ContextCfg
 */

namespace Commune\Chatbot\Demo\Configure\ContextCfg;

use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Routing\DialogRoute;

class Root extends ContextCfg
{
    const SCOPE = [Scope::SESSION];

    public function routing(DialogRoute $route)
    {
        $route->fallback()
            ->action(function(Context $context, Intent $intent){
                $context->info('收到输入:' .$intent->getMessage()->getText());
            }) ;

        $route->hearsRegex('test')
            ->info('进入test单元')
            ->to(Test::class);

        $route->hearsRegex('test callback')
            ->action(function(Context $context, Intent $intent){
                $context->ask('sayAnswer', '测试回调逻辑, 请输入回答');
            });

        $route->hearsRegex('test confirm')
            ->action(function(Context $context, Intent $intentData){
                $context->confirm('sayConfirm', '测试确认功能, 请尝试回答括号里的内容', 'true');
            });

        $route->callback('sayAnswer')
            ->callSelfMethod('sayAnswer');

        $route->callback('sayConfirm')
            ->action(function(Context $context, Intent $intent){
                $message = '确定结果为: ';
                $message.= $intent['confirmation'] ? 'true' : 'false';
                $context->info( $message);
            });
    }

    public function prepared(Context $context)
    {
        $context->info('你好, 这里是测试根目录. 请输入/h 查看可用指令.');
    }

    public function waking(Context $context)
    {
        $this->prepared($context);
    }

    public function sayAnswer(Context $context, Intent $intent)
    {
        $context->info('回答是 : '. $intent['answer']);
    }


}