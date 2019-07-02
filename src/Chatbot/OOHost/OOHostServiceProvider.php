<?php


namespace Commune\Chatbot\OOHost;


use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\ConsoleLogger;
use Commune\Chatbot\Framework\Component\Providers\LoadPsr4SelfRegister;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Registrar;
use Commune\Chatbot\OOHost\Context\Memory\MemoryBagDefinition;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\Registrar as IntentRegistrarInterface;

class OOHostServiceProvider extends BaseServiceProvider
{
    const IS_REACTOR_SERVICE_PROVIDER = true;

    public function boot($app)
    {
        /**
         * @var ChatbotConfig $chatbotConfig
         */
        $chatbotConfig = $app[ChatbotConfig::class];
        $host = $chatbotConfig->host;

        $repo = MemoryRegistrar::getIns();
        foreach ($host->memories as $memoryOption) {
            $repo->register(
                new MemoryBagDefinition(
                    $memoryOption->name,
                    $memoryOption->scopes,
                    $memoryOption->desc,
                    $memoryOption->entities
                )
            );
        }

        foreach ($host->autoloadPsr4 as $namespace => $path) {
            LoadPsr4SelfRegister::loadSelfRegister(
                $namespace,
                $path,
                $app[ConsoleLogger::class]
            );
        }
    }

    public function register()
    {
        $this->app->singleton(Registrar::class, function(){
            return ContextRegistrar::getIns();
        });

        $this->app->singleton(IntentRegistrarInterface::class, function(){
            return IntentRegistrar::getIns();
        });

    }

}