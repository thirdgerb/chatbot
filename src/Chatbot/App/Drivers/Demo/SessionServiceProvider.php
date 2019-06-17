<?php


namespace Commune\Chatbot\App\Drivers\Demo;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\OOHost\Session\Driver;

class SessionServiceProvider extends ServiceProvider
{
    public function boot($app): void
    {
    }

    public function register(): void
    {
        $this->app->singleton(Driver::class, ArraySessionDriver::class);
    }


}