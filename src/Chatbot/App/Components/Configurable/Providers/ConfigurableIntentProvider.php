<?php


namespace Commune\Chatbot\App\Components\Configurable\Providers;


use Commune\Chatbot\App\Components\Configurable\Configs\DomainConfig;
use Commune\Chatbot\App\Components\Configurable\Contexts\ConfigurableIntentDef;
use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Memory\MemoryBagDefinition;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrar;
use Commune\Container\ContainerContract;

class ConfigurableIntentProvider extends ServiceProvider
{
    /**
     * @var DomainConfig
     */
    protected $config;

    /**
     * ConfigurableIntentProvider constructor.
     * @param DomainConfig $config
     * @param ContainerContract $app
     */
    public function __construct(DomainConfig $config, $app)
    {
        $this->config = $config;
        parent::__construct($app);
    }

    public function boot($app)
    {
        $this->preloadMemories();
        $this->preloadIntents();
    }

    protected function preloadMemories() : void
    {
        $repo = MemoryRegistrar::getIns();
        foreach($this->config->memories as $memoryOption) {
            $repo->register(new MemoryBagDefinition(
                $memoryOption->name,
                $memoryOption->scopes,
                $memoryOption->desc,
                []
            ));
        }

    }

    protected function preloadIntents() : void
    {
        $repo = IntentRegistrar::getIns();
        foreach ($this->config->intents as $intentConfig) {
            $repo->register(new ConfigurableIntentDef(
                $this->config,
                $intentConfig
            ));
        }
    }

    public function register()
    {
    }


}