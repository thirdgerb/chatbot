<?php


namespace Commune\Chatbot\OOHost;

use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\ConsoleLogger;
use Commune\Chatbot\Framework\Component\Providers\LoadPsr4SelfRegister;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Memory\MemoryBagDefinition;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrar;
use Commune\Chatbot\OOHost\Context\Registrar as ContextRegistrarInterface;
use Commune\Chatbot\OOHost\Context\Intent\Registrar as IntentRegistrarInterface;
use Commune\Chatbot\OOHost\Context\Memory\Registrar as MemoryRegistrarInterface;
use Commune\Chatbot\OOHost\Emotion\Feeling;
use Commune\Chatbot\OOHost\Emotion\Feels;


class HostProcessServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    public function boot($app)
    {
        /**
         * @var ChatbotConfig $chatbotConfig
         */
        $chatbotConfig = $app[ChatbotConfig::class];
        $host = $chatbotConfig->host;

        $repo = $app->get(MemoryRegistrarInterface::class);
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
        $this->registerContextRegistrar();
        $this->registerIntentRegistrar();
        $this->registerMemoryRegistrar();
        $this->registerFeeling();
    }

    protected function registerContextRegistrar()
    {
        $this->app->singleton(ContextRegistrarInterface::class, function(){
            return ContextRegistrar::getIns();
        });
    }

    protected function registerIntentRegistrar()
    {
        $this->app->singleton(IntentRegistrarInterface::class, function(){
            return IntentRegistrar::getIns();
        });
    }

    protected function registerMemoryRegistrar()
    {
        $this->app->singleton(MemoryRegistrarInterface::class, function(){
            return MemoryRegistrar::getIns();
        });
    }

    protected function registerFeeling()
    {
        $this->app->singleton(Feeling::class, Feels::class);
    }


}