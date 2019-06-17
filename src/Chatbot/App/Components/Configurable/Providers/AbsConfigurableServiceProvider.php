<?php


namespace Commune\Chatbot\App\Components\Configurable\Providers;


use Commune\Chatbot\App\Components\ConfigurableComponent;
use Commune\Chatbot\Blueprint\ServiceProvider;

abstract class AbsConfigurableServiceProvider extends ServiceProvider
{
    /**
     * @var ConfigurableComponent
     */
    protected $config;

    public function __construct($app, ConfigurableComponent $config)
    {
        $this->config = $config;
        parent::__construct($app);
    }

}