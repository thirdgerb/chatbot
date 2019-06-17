<?php


namespace Commune\Chatbot\App\Drivers\Demo;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Contracts\CacheAdapter;

class CacheServiceProvider extends ServiceProvider
{
    public function boot($app): void
    {
    }

    public function register(): void
    {
        $this->app->singleton(CacheAdapter::class, ArrayCache::class);
    }


}