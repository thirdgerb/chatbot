<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Providers;

use Commune\Container\ContainerContract;
use Commune\Contracts\Ghost\RuntimeDriver;
use Commune\Contracts\ServiceProvider;
use Commune\Framework\RuntimeDriver\DemoRuntimeDriver;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RuntimeDriverDemoProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [];
    }

    public function getDefaultScope(): string
    {
        return self::SCOPE_REQ;
    }


    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(
            RuntimeDriver::class,
            DemoRuntimeDriver::class
        );
    }


}