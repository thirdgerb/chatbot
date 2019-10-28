<?php


namespace Commune\Components\UnheardLike\Providers;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\OOHost\Context\Contracts\RootContextRegistrar;
use Commune\Components\UnheardLike\Libraries\UnheardRegistrar;
use Commune\Container\ContainerContract;

class UnheardLikeServiceProvider extends ServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    /**
     * @param ContainerContract $app
     */
    public function boot($app)
    {
        /**
         * @var RootContextRegistrar $repo
         */
        $repo = $app[RootContextRegistrar::class];
        $repo->registerSubRegistrar(UnheardRegistrar::class);
    }

    public function register()
    {
        $this->app->singleton(UnheardRegistrar::class);
    }


}