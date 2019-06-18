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


        // 注册reactor 的服务, reactor 进程内各个请求内共享.
        $app->getConsoleLogger()->debug("load reactor providers : ");
        foreach ($config->reactorProviders as $providerName) {
            $app->getConsoleLogger()->debug("load reactor provider $providerName");
            $app->registerReactorService($providerName);
        }

        // 注册conversation 的服务, 同一个reactor 进程内每个请求相隔离.
        $app->getConsoleLogger()->debug("load conversation providers : ");
        foreach ($config->conversationProviders as $providerName) {
            $app->getConsoleLogger()->debug("load conversation provider $providerName");
            $app->registerConversationService($providerName);
        }

    }

}