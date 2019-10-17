<?php


namespace Commune\Chatbot\OOHost;


use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Container\ContainerContract;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Dialogue\Hearing\HearingHandler;
use Commune\Chatbot\OOHost\Session\Driver;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionImpl;

class HostConversationalServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = false;

    /**
     * @param ContainerContract $app
     */
    public function boot($app)
    {
    }

    public function register()
    {
        $this->registerHearing();
        $this->registerSession();
    }


    protected function registerSession() : void
    {
        $this->app->bind(Session::class, function($conversation, $parameters){
            return new SessionImpl(
                $parameters[Session::SESSION_ID_VAR],
                $conversation[ChatbotConfig::class]->host,
                $conversation,
                $conversation[Driver::class]
            );
        });
    }

    protected function registerHearing() : void
    {
        // 可以重写成自己觉得合适的
        $this->app->bind(
            Hearing::class,
            function($app, $parameters){
                return new HearingHandler(
                    $parameters['context'],
                    $parameters['dialog'],
                    $parameters['message']
                );
            }
        );
    }



}