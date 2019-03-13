<?php

/**
 * Class Application
 * @package Commune\Chatbot\Demo\Impl
 */

namespace Commune\Chatbot\Demo\Impl;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Contracts\ExceptionHandler;
use Commune\Chatbot\Contracts\ServerDriver;
use Commune\Chatbot\Framework\Character\User;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Routing\IntentRoute;
use Commune\Chatbot\Framework\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Container\Container;

class ChatbotAppDemo implements ChatbotApp
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var Container
     */
    protected $app;

    /**
     * @var IntentRoute
     */
    protected $defIntentRoute;

    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->config = $this->getChatbotConfig();
    }

    public function getChatbotConfig(): array
    {
        return include __DIR__ . '/../config.php';

    }

    public function isSupervisor(User $sender): bool
    {
        return true;
    }

    public function getContainer(): Container
    {
        return $this->app;
    }


    public function getServerDriver(): ServerDriver
    {
        $driver = $this->make(ServerDriver::class);
        return $driver;
    }

    public function getExceptionHandler(): ExceptionHandler
    {
        return $this->make(ExceptionHandler::class);
    }


    public function getIntentDefaultRoute(Router $router): IntentRoute
    {
        if (!isset($this->defIntentRoute)) {
            $this->defIntentRoute = new IntentRoute( $this, $router, static::class);
            $this->defIntentRoute
                ->action()
                ->call(function (Context $context, Intent $intent) {
                    $context->error('miss match intent : ' . $intent);
                })
                ->redirect()
                ->home();

        }

        return $this->defIntentRoute;
    }

    public function make($abstract, array $parameters = [])
    {
        return $this->app->make($abstract, $parameters);
    }

    public function get($id)
    {
        return $this->app->get($id);
    }

    public function has($id)
    {
        return $this->app->has($id);
    }

    public function getConfig(string $configConstantName, $default = null)
    {
        return Arr::get($this->config, $configConstantName, $default);
    }


}