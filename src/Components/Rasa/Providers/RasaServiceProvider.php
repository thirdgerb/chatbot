<?php


namespace Commune\Components\Rasa\Providers;


use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Components\Rasa\Services\CorpusSynchrony;
use Commune\Components\Rasa\Services\RasaService;

class RasaServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = false;

    public function boot($app)
    {
    }

    public function register()
    {
        $this->app->singleton(RasaService::class);
        $this->app->singleton(CorpusSynchrony::class);
    }


}