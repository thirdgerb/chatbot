<?php

namespace Commune\Chatbot\OOHost\NLU\Providers;

use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Context\Contracts\RootIntentRegistrar;
use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Contracts\EntityExtractor;
use Commune\Chatbot\OOHost\NLU\Corpus\CorpusRepository;
use Commune\Chatbot\OOHost\NLU\Libraries\PHPEntityExtractor;
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
                    $app[RootIntentRegistrar::class],
                    $app[OptionRepository::class]
                );
            });
        }

        // 注册 entity extractor
        if (!$this->app->bound(EntityExtractor::class)) {
            $this->app->singleton(EntityExtractor::class, PHPEntityExtractor::class);
        }
    }


}