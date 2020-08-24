<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\SpaCyNLU\Providers;

use Commune\Blueprint\NLU\NLUManager;
use Commune\Components\SpaCyNLU\SpaCyNLUComponent;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SpaCyNLURegisterProvider extends ServiceProvider
{
    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
    }

    public static function stub(): array
    {
        return [];
    }

    public function boot(ContainerContract $app): void
    {
        /**
         * @var SpaCyNLUComponent $config
         * @var NLUManager $manager
         */
        $config = $app->make(SpaCyNLUComponent::class);
        $manager = $app->make(NLUManager::class);

        $manager->registerService($config->nluServiceOption);
        $manager->registerService($config->chatServiceOption);
    }

    public function register(ContainerContract $app): void
    {
    }


}