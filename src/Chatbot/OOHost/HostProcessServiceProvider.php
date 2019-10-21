<?php


namespace Commune\Chatbot\OOHost;

use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\ConsoleLogger;
use Commune\Chatbot\Framework\Component\Providers\LoadPsr4SelfRegister;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrarDefault;
use Commune\Chatbot\OOHost\Context\Intent\RootIntentRegistrarImpl;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrarDefault;
use Commune\Chatbot\OOHost\Context\Memory\RootMemoryRegistrarImpl;
use Commune\Chatbot\OOHost\Context\Memory\MemoryBagDefinition;
use Commune\Chatbot\OOHost\Context\Contracts\RootMemoryRegistrar;
use Commune\Chatbot\OOHost\Context\Contracts;
use Commune\Chatbot\OOHost\Context\Registrar\RootContextRegistrarDefault;
use Commune\Chatbot\OOHost\Context\Registrar\RootContextRegistrarImpl;
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

        // 注册 intent 和 memory 到父容器
        /**
         * @var Contracts\RootContextRegistrar $contextRepo
         */
        $contextRepo = $app[Contracts\RootContextRegistrar::class];
        $contextRepo->registerSubRegistrar(Contracts\RootIntentRegistrar::class);
        $contextRepo->registerSubRegistrar(Contracts\RootMemoryRegistrar::class);

        // 注册在host 配置中定义的 memories
        // register memories defined at host config
        /**
         * @var RootMemoryRegistrar $repo
         */
        $repo = $app->get(RootMemoryRegistrar::class);
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
        $this->app->singleton(Contracts\RootContextRegistrar::class, function($app){

            return new RootContextRegistrarImpl(
                $app[Application::class],
                RootContextRegistrarDefault::class
            );
        });

        $this->app->singleton(RootContextRegistrarDefault::class);
    }

    protected function registerIntentRegistrar()
    {
        $this->app->singleton(Contracts\RootIntentRegistrar::class, function($app){

            $registrar = new RootIntentRegistrarImpl(
                $app[Application::class],
                IntentRegistrarDefault::class
            );
            return $registrar;
        });

        $this->app->singleton(IntentRegistrarDefault::class);
    }

    protected function registerMemoryRegistrar()
    {
        $this->app->singleton(RootMemoryRegistrar::class, function($app){
            $registrar = new RootMemoryRegistrarImpl(
                $app[Application::class],
                MemoryRegistrarDefault::class
            );

            return $registrar;
        });

        $this->app->singleton(MemoryRegistrarDefault::class);
    }

    protected function registerFeeling()
    {
        $this->app->singleton(Feeling::class, Feels::class);
    }


}