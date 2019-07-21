<?php

/**
 * Class RegisterProviders
 * @package Commune\Chatbot\Framework\Bootstrap
 */

namespace Commune\Chatbot\Framework\Bootstrap;

use Commune\Chatbot\Blueprint\Application;

/**
 * Class RegisterProviders
 * @package Commune\Chatbot\Framework\Bootstrap
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RegisterProviders implements Bootstrapper
{

    public function bootstrap(Application $app) : void
    {

        $config = $app->getConfig();


        // 注册worker process 的服务, worker 进程内各个请求内共享.
        $app->getConsoleLogger()->debug("load process providers : ");
        foreach ($config->processProviders as $providerName) {
            $app->getConsoleLogger()->debug("load process provider $providerName");
            $app->registerProcessService($providerName);
        }

        // 注册conversation 的服务, 同一个 worker 进程内每个请求相隔离.
        $app->getConsoleLogger()->debug("load conversation providers : ");
        foreach ($config->conversationProviders as $providerName) {
            $app->getConsoleLogger()->debug("load conversation provider $providerName");
            $app->registerConversationService($providerName);
        }

    }

}