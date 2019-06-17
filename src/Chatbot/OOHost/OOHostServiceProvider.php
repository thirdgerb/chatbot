<?php


namespace Commune\Chatbot\OOHost;


use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Context\Memory\MemoryBagDefinition;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrar;

class OOHostServiceProvider extends BaseServiceProvider
{
    const IS_REACTOR_SERVICE_PROVIDER = true;

    public function boot($app)
    {
        /**
         * @var ChatbotConfig $chatbotConfig
         */
        $chatbotConfig = $app[ChatbotConfig::class];
        $host = $chatbotConfig->host;

        $repo = MemoryRegistrar::getIns();
        foreach ($host->memories as $memoryOption) {
            $repo->register(
                new MemoryBagDefinition(
                    $memoryOption->name,
                    $memoryOption->scopes,
                    $memoryOption->desc,
                    $memoryOption->entities
                )
            );
        }
    }

    public function register()
    {
    }


}