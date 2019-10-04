<?php


namespace Commune\Chatbot\Framework\Providers;


use Commune\Chatbot\Contracts\ExceptionHandler;
use Commune\Chatbot\Framework\Exceptions\FatalExceptionHandler;
use Commune\Container\ContainerContract;

class ExpHandlerServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    /**
     * @param ContainerContract $app
     */
    public function boot($app): void
    {
    }

    public function register(): void
    {
        if ($this->app->bound(ExceptionHandler::class)) {
            return;
        }

        $this->app->singleton(ExceptionHandler::class, FatalExceptionHandler::class);
    }


}