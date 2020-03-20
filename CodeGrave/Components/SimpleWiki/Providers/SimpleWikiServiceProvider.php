<?php


namespace Commune\Components\SimpleWiki\Providers;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\OOHost\Context\Contracts\RootIntentRegistrar;
use Commune\Components\SimpleWiki\Libraries\SimpleWikiRegistrar;
use Commune\Container\ContainerContract;

class SimpleWikiServiceProvider extends ServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    /**
     * @param ContainerContract $app
     */
    public function boot($app)
    {
        /**
         * @var RootIntentRegistrar $repo
         */
        $repo = $app[RootIntentRegistrar::class];
        $repo->registerSubRegistrar(SimpleWikiRegistrar::class);
    }

    public function register()
    {
        $this->app->singleton(SimpleWikiRegistrar::class);
    }


}