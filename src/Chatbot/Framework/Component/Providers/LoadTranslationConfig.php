<?php


namespace Commune\Chatbot\Framework\Component\Providers;


use Commune\Container\ContainerContract;
use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Framework\Providers\TranslationLoader;

class LoadTranslationConfig extends ServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    use TranslationLoader;

    /**
     * @var string
     */
    protected $resourcePath;

    /**
     * @var string
     */
    protected $loader;

    public function __construct(
        ContainerContract $app,
        string $path,
        string $loader = Translator::FORMAT_PHP
    )
    {
        $this->resourcePath = $path;
        $this->loader = $loader;
        parent::__construct($app);
    }

    public function boot($app)
    {
        $this->loading($app);
    }

    protected function getResourcePath($app): string
    {
        return $this->resourcePath;
    }

    protected function getLoader($app): string
    {
        return $this->loader;
    }

    public function register()
    {
    }


}