<?php


namespace Commune\Chatbot\Framework\Bootstrap;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;

/**
 * @deprecated
 */
class RegisterBaseServices implements Bootstrapper
{
    public function bootstrap(Application $app): void
    {
        $app->getReactorLogger()->info('start register base service provider');

        $config = $app->getConfig();
        $log = $app->getReactorLogger();

        foreach ($config->baseProviders as $providerName) {

            if (!is_a($providerName, BaseServiceProvider::class, TRUE)) {
                throw new ConfigureException(
                    static::class
                    . ' only accept provider instance of BaseServiceProvider, '
                    . ' '.$providerName . ' given'
                );
            }

            $isReactor = constant("$providerName::IS_REACTOR_SERVICE_PROVIDER");


            if ($isReactor) {
                $app->registerReactorService($providerName);
            } else {
                $app->registerConversationService($providerName);
            }

            $log->debug(
                'register base '
                . ($isReactor ? 'reactor ' : 'conversational ')
                . 'service provider : '
                . $providerName
            );


        }


    }
}