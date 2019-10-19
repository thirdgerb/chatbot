<?php

namespace Commune\Chatbot\OOHost\NLU\Providers;

use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Contracts\NLULogger;
use Commune\Chatbot\OOHost\NLU\Corpus\CorpusRepository;
use Commune\Chatbot\OOHost\NLU\NLUComponent;
use Commune\Container\ContainerContract;
use Commune\Support\OptionRepo\Contracts\OptionRepository;


/**
 * nlu 相关组件的注册服务.
 */
class CorpusServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    public function boot($app)
    {
    }

    public function register()
    {
        // 注册 copus
        if (!$this->app->bound(Corpus::class)) {
            $this->app->singleton(Corpus::class, function($app){
                return new CorpusRepository(
                    $app[IntentRegistrar::class],
                    $app[OptionRepository::class]
                );
            });
        }
    }


}