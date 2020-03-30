<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Platforms\ReactStdio;

use Commune\Container\ContainerContract;
use Commune\Framework\Contracts\ServiceProvider;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RSPlatformServiceProvider extends ServiceProvider
{

    public static function stub(): array
    {
        return [

        ];
    }

    public function isProcessServiceProvider(): bool
    {
        return true;
    }

    public function boot(ContainerContract $app): void
    {
        // TODO: Implement boot() method.
    }

    public function register(ContainerContract $app): void
    {
        // TODO: Implement register() method.
    }



}