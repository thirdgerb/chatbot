<?php


namespace Commune\Chatbot\OOHost;

use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\ConsoleLogger;
use Commune\Chatbot\Framework\Component\Providers\LoadPsr4SelfRegister;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\ContextRegistrarImpl;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrarImpl;
use Commune\Chatbot\OOHost\Context\Memory\MemoryBagDefinition;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrar;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrarImpl;
use Commune\Chatbot\OOHost\Context\ContextRegistrar as ContextRegistrarInterface;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar as IntentRegistrarInterface;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrar as MemoryRegistrarInterface;
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


        // 注册在host 配置中定义的 memories
        // register memories defined at host config
        /**
         * @var MemoryRegistrar $repo
         */
        $repo = $app->get(MemoryRegistrarInterface::class);
        foreach ($host->memories as $memoryOption) {
            $repo->registerDef(
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
                $app,
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
        $this->app->singleton(ContextRegistrarInterface::class, function($app){
            return new ContextRegistrarImpl($app[Application::class]);
        });
    }

    protected function registerIntentRegistrar()
    {
        $this->app->singleton(IntentRegistrarInterface::class, function($app){

            $registrar = new IntentRegistrarImpl($app[Application::class],$app[ContextRegistrar::class]);
            return $registrar;
        });
    }

    protected function registerMemoryRegistrar()
    {
        $this->app->singleton(MemoryRegistrarInterface::class, function($app){
            $registrar = new MemoryRegistrarImpl($app[Application::class], $app[ContextRegistrar::class]);
            return $registrar;
        });
    }

    protected function registerFeeling()
    {
        $this->app->singleton(Feeling::class, Feels::class);
    }


}