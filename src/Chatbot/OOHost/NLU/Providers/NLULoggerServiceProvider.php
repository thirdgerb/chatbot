<?php


namespace Commune\Chatbot\OOHost\NLU\Providers;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\OOHost\NLU\Contracts\NLULogger;
use Commune\Chatbot\OOHost\NLU\NLUComponent;
use Commune\Container\ContainerContract;

class NLULoggerServiceProvider extends ServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = false;

    /**
     * @var NLUComponent
     */
    protected $config;


    public function __construct(ContainerContract $app, NLUComponent $config)
    {
        $this->config = $config;
        parent::__construct($app);
    }


    /**
     * @param ContainerContract $app
     */
    public function boot($app)
    {
    }

    public function register()
    {
        if (!$this->app->bound(NLULogger::class)) {
            $this->app->singleton(NLULogger::class, $this->config->nluLogger);
        }
    }


}