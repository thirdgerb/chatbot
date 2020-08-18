<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\NLU;

use Commune\Blueprint\NLU\NLUManager;
use Commune\Comprehenders\Manager\INLUManager;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class NLUServiceProvider extends ServiceProvider
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
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(
            NLUManager::class,
            INLUManager::class
        );
    }


}