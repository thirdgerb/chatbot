<?php


namespace Commune\Chatbot\OOHost\NLU\Providers;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\OOHost\NLU\Contracts\NLULogger;
use Commune\Chatbot\OOHost\NLU\Predefined\SimpleNLULogger;
use Commune\Container\ContainerContract;

class NLULoggerServiceProvider extends ServiceProvider
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
        if (!$this->app->bound(NLULogger::class)) {
            $this->app->singleton(NLULogger::class, SimpleNLULogger::class);
        }
    }


}