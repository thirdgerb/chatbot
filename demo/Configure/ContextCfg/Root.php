<?php

/**
 * Class Root
 * @package Commune\Chatbot\Demo\Configure\ContextCfg
 */

namespace Commune\Chatbot\Demo\Configure\ContextCfg;

use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Message\Text;
use Commune\Chatbot\Framework\Routing\DialogRoute;

class Root extends ContextCfg
{
    const SCOPE = [Scope::SESSION];

    const DATA = [
        'testing' => 'abc'
    ];

    public function routing(DialogRoute $route)
    {
        $route->fallback()
            ->action()
            ->call(function(Context $context, Intent $intent){
                $context->info('收到输入:' .$intent->getMessage()->getText());
            });

        $route->hears('multi')
            ->action()
            ->info('123')
            ->info('234');


        $route->hears('test middleware')
            ->action()
            ->info('test middleware');

        $route->hears('test')
            ->action()
            ->info('进入test单元')
                ->redirect()
                    ->to(Test::class);

        $route->hears('test question through middleware')
            ->middleware(function(Conversation $conversation, \Closure $next){
                $conversation->reply(new Text('hit middleware'));
                return $next($conversation);
            })
            ->action()
                ->call(function(Context $context, Intent $intent){
                    return $context->ask('sayAnswer', '测试回调逻辑, 请输入回答');
                });

        $route->hears('test confirm')
            ->action()
            ->call(function(Context $context, Intent $intentData){
                return $context->confirm('sayConfirm', '测试确认功能, 请尝试回答括号里的内容', 'true');
            });

        $route->hears('test then')
            ->action()
            ->info('测试 then 的回调逻辑')
            ->redirect()
                ->then(function(Context $context, Intent $intent) {
                    return $context->ask(
                        'sayAnswer',
                        '用来确认回调成功的问题, 意图为:'.$intent->getId(),
                        '回调成功'
                    );
                });

        $route->hears('test choice')
            ->action()
            ->info('测试 选择功能 + 选择路由')
            ->redirect()
                ->choose(
                    'sayChoice',
                    '测试选择题加模板 {}',
                        [
                            '选项1',
                            '选项2',
                            '选项3',
                        ],
                        2,
                        ['testing']
                );

        $route->hearsCommand('test:cmd 
            {arg1=123 : 用来测试的参数arg1}
            ')
            ->exactly('cmd')
            ->exactly('testCmd')
            ->action()
            ->call(function(Context $context, Intent $intent) {
                $context->info('命中命令: test:cmd');
                $context->info('参数 arg1: '.$intent['arg1']);
            });

        $route->name('sayChoice')
            ->action()
            ->call(function (Context $context, Intent $intent){
                $context->info('选择为: '. $intent['result']);
            });


        $route->name('sayAnswer')
            ->action()
            ->callSelf('sayAnswer');

        $route->name('sayConfirm')
            ->action()
            ->call(function(Context $context, Intent $intent){
                $message = '确定结果为: ';
                $message.= $intent['result'] ? 'true' : 'false';
                $context->info( $message);
            });

    }

    public function prepared(Context $context)
    {
        $context->info($this->getDescription($context));
    }

    public function waking(Context $context)
    {
        $this->prepared($context);
    }

    public function getDescription(Context $context) : string
    {
        return '你好, 这里是测试根目录. 请输入.h 查看可用指令.';
    }

    public function sayAnswer(Context $context, Intent $intent)
    {
        $context->info('命中回答回调. 回答是 : '. $intent['result']);
    }


}