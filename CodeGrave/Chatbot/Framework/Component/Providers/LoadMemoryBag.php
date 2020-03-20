<?php

/**
 * Class LoadMemoryBag
 * @package Commune\Chatbot\Framework\Component\Providers
 */

namespace Commune\Chatbot\Framework\Component\Providers;


use Commune\Chatbot\Config\Options\MemoryOption;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Context\Memory\MemoryBagDefinition;
use Commune\Chatbot\OOHost\Context\Contracts\RootMemoryRegistrar;
use Commune\Container\ContainerContract;

class LoadMemoryBag extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    /**
     * @var array
     */
    protected $memoryOptions;

    /**
     * LoadMemoryBag constructor.
     * @param ContainerContract $app
     * @param array $memoryOptions
     */
    public function __construct(ContainerContract $app, array $memoryOptions)
    {
        $this->memoryOptions = $memoryOptions;
        parent::__construct($app);
    }


    public function boot($app)
    {
        /**
         * @var RootMemoryRegistrar $memoryRepo
         */
        $memoryRepo = $app->get(RootMemoryRegistrar::class);

        foreach ($this->memoryOptions as $option) {
            $memoryOption = new MemoryOption($option);
            $memoryRepo->registerDef(
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