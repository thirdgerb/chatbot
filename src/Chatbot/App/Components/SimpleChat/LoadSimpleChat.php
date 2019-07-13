<?php


namespace Commune\Chatbot\App\Components\SimpleChat;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Symfony\Component\Finder\Finder;

/**
 * service provider
 */
class LoadSimpleChat extends ServiceProvider
{

    /**
     * @var SimpleChatOption
     */
    protected $config;

    public function __construct($app, SimpleChatOption $option)
    {
        $this->config = $option;
        parent::__construct($app);
    }

    public function boot($app)
    {
        Manager::loadResource($this->config->id, $this->config->resource);
    }

    public function register()
    {
    }


}