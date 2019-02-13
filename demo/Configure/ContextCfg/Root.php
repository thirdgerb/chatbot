<?php

/**
 * Class Root
 * @package Commune\Chatbot\Demo\Configure\ContextCfg
 */

namespace Commune\Chatbot\Demo\Configure\ContextCfg;

use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Intent\IntentData;
use Commune\Chatbot\Framework\Routing\DialogRoute;

class Root extends ContextCfg
{
    const SCOPE = [Scope::SESSION];

    public function routing(DialogRoute $route)
    {
        $route->fallback()
            ->action(function(Context $context, IntentData $intent){
                $context->info('收到输入:' .$intent->getMessage()->getText());
            }) ;

        $route->hearsRegex('test')
            ->info('进入test单元')
            ->to(Test::class);
    }

    public function prepared(Context $context)
    {
        $context->info('你好, 这里是测试根目录. 请输入/h 查看可用指令.');
    }

    public function waking(Context $context)
    {
        $this->prepared($context);
    }


}