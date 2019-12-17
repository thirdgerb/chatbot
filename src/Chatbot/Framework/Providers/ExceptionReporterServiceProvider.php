<?php


namespace Commune\Chatbot\Framework\Providers;


use Commune\Chatbot\Contracts\ExceptionReporter;
use Commune\Chatbot\Framework\Exceptions\ExceptionReporterImpl;
use Commune\Container\ContainerContract;

/**
 * 进程级
 */
class ExceptionReporterServiceProvider extends BaseServiceProvider
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
        if ($this->app->bound(ExceptionReporter::class)) {
            return;
        }

        $this->app->singleton(ExceptionReporter::class, ExceptionReporterImpl::class);
    }


}