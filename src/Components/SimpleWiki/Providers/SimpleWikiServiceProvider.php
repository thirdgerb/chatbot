<?php


namespace Commune\Components\SimpleWiki\Providers;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
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
         * @var SimpleWikiRegistrar $repo
         * 这一步执行了注册. 使$repo 注册到 intentRegistrar
         */
        $app[SimpleWikiRegistrar::class];
    }

    public function register()
    {
        $this->app->singleton(SimpleWikiRegistrar::class, function($app){
            return new SimpleWikiRegistrar(
                $app[Application::class],
                $app[IntentRegistrar::class]
            );
        });
    }


}