<?php


namespace Commune\Chatbot\App\Components\Rasa;


use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Container\ContainerContract;

class RasaServiceProvider extends BaseServiceProvider
{
    const IS_REACTOR_SERVICE_PROVIDER = false;

    /**
     * @var mixed
     */
    protected $nluPipeImplement;

    public function __construct($app, $nluPipeImplement)
    {
        $this->nluPipeImplement = $nluPipeImplement;
        parent::__construct($app);
    }

    public function boot($app)
    {
    }

    public function register()
    {
        $this->app->singleton(RasaNLUPipe::class, $this->nluPipeImplement);
    }


}