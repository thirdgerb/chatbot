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
        $logger = $app->getConsoleLogger();

        // base process
        $logger->debug("load base providers : ");
        $baseServices = $config->baseServices;
        foreach ($baseServices as $name => $providerName) {
            $isProcessProvider = constant("$providerName::IS_PROCESS_SERVICE_PROVIDER");

            if ($isProcessProvider) {
                $logger->debug("register base process service provider $providerName");
                $app->registerProcessService($providerName);
            } else {
                $logger->debug("register base conversation service provider $providerName");
                $app->registerConversationService($providerName);
            }
        }

        // 注册worker process 的服务, worker 进程内各个请求内共享.
        $logger->debug("load process providers : ");
        foreach ($config->processProviders as $providerName) {
            $logger->debug("load process provider $providerName");
            $app->registerProcessService($providerName);
        }

        // 注册conversation 的服务, 同一个 worker 进程内每个请求相隔离.
        $logger->debug("load conversation providers : ");
        foreach ($config->conversationProviders as $providerName) {
            $logger->debug("load conversation provider $providerName");
            $app->registerConversationService($providerName);
        }

    }

}