<?php


namespace Commune\Chatbot\App\Components\Configurable\Providers;

use Commune\Chatbot\App\Components\Configurable\Drivers\DomainConfigRepository;
use Commune\Chatbot\App\Components\Configurable\Drivers\JsonDomainRepository;

class JsonLoaderServiceProvider extends AbsConfigurableServiceProvider
{

    public function boot($app)
    {
        /**
         * @var DomainConfigRepository $repo
         */
        $repo = $app[DomainConfigRepository::class];
        $repo->preload();
        foreach ($this->config->resources as $resource) {
            $repo->addResource($resource);
        }
    }

    public function register()
    {
        $defaultPath = $this->config->defaultPath;

        $this->app->singleton(
            DomainConfigRepository::class,
            function() use ($defaultPath){
                return new JsonDomainRepository($defaultPath);
            }
        );
    }


}