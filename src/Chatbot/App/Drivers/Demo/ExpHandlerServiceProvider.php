<?php


namespace Commune\Chatbot\App\Drivers\Demo;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Contracts\ExceptionHandler;
use Commune\Container\ContainerContract;

class ExpHandlerServiceProvider extends ServiceProvider
{
    public function boot($app): void
    {
    }

    public function register(): void
    {
        $this->app->singleton(ExceptionHandler::class, SimpleExpHandler::class);
    }


}