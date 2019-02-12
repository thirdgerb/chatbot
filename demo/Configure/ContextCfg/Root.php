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
                $context->info('receive:' .$intent->getMessage()->getText());
            }) ;

        $route->hearsRegex('test')
            ->info('go test')
            ->error('go test !!!!!!!!!!')
            ->to(Test::class);
    }

    public function created(Context $context)
    {
        $context->info(static::class . '::initial');
    }

    public function waked(Context $context)
    {
        $context->info(static::class . '::wake');
    }

    public function prepared(Context $context)
    {
        $context->info(static::class . '::prepared');
    }

    public function restored(Context $context)
    {
        $context->info(static::class . '::restored');
    }


}