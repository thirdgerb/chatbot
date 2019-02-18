<?php

/**
 * Class Preloader
 * @package Commune\Chatbot\Framework\Bootstrap
 */

namespace Commune\Chatbot\Framework\Bootstrap;


use Commune\Chatbot\Command\Command;
use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\HostDriver;
use Commune\Chatbot\Framework\Routing\Router;

class PreloadContextConfig implements Bootstrapper
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * PreloadContextConfig constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }


    /**
     * @param ChatbotApp $app
     * @throws \ReflectionException
     */
    public function bootstrap(ChatbotApp $app)
    {

        $this->registerPipes($app);

        // 必要, 方便各处获取 host 相关的组件.
        $ioc = $app->getContainer();

        // host Driver
        $ioc->singleton(HostDriver::class);

        $this->preloadContexts($app);
        $this->preloadCommands($app);

    }

    protected function registerPipes(ChatbotApp $app)
    {
        $ioc = $app->getContainer();

        foreach($app->getConfig(ChatbotApp::RUNTIME_PIPES) as $pipeName) {
            $ioc->singleton($pipeName);
        }
    }

    /**
     * @param ChatbotApp $app
     * @throws \ReflectionException
     */
    protected function preloadCommands(ChatbotApp $app)
    {
        $c1 = $app->getConfig(ChatbotApp::RUNTIME_ANALYZERS);
        $c2 = $app->getConfig(ChatbotApp::RUNTIME_USER_COMMANDS);

        $commands = array_unique(array_merge($c1, $c2));

        $ioc = $app->getContainer();
        foreach($commands as $commandName) {

            $r = new \ReflectionClass($commandName);
            if (!$r->isSubclassOf(Command::class)) {
                //todo
                throw new ConfigureException();
            }
            if ($r->getConstant('SINGLETON')) {
                $ioc->singleton($commandName);
            } else {
                $ioc->bind($commandName, $commandName);
            }

        }
    }

    protected function preloadContexts(ChatbotApp $app)
    {
        $rootContext = $app->getConfig(ChatbotApp::CONTEXT_ROOT);
        $contextConfigs = $app->getConfig(ChatbotApp::CONTEXT_PRELOAD);
        array_unshift($contextConfigs, $rootContext);

        $ioc = $app->getContainer();

        foreach($contextConfigs as $contextCfgName) {
            $ioc->singleton($contextCfgName);
            $this->router->loadContextConfig($contextCfgName);
        }

    }
}