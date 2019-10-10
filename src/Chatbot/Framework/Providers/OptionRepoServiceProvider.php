<?php


namespace Commune\Chatbot\Framework\Providers;


use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Commune\Support\OptionRepo\Impl\OptionRepositoryImpl;
use Commune\Support\OptionRepo\Storage;

class OptionRepoServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    public function boot($app)
    {
    }

    public function register()
    {
        $this->registerOptionRepository();
        $this->registerDefaultStorage();
    }

    protected function registerOptionRepository()
    {
        if (!$this->app->bound(OptionRepository::class)) {
            $this->app->singleton(OptionRepository::class, OptionRepositoryImpl::class);
        }
    }

    protected function registerDefaultStorage()
    {
        $this->app->singleton(Storage\Memory\MemoryStorage::class);
        $this->app->singleton(Storage\Yaml\YamlRootStorage::class);
        $this->app->singleton(Storage\Json\JsonRootStorage::class);
        $this->app->singleton(Storage\Arr\PHPRootStorage::class);
    }

}