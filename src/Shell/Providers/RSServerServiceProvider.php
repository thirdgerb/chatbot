<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Providers;

use Commune\Container\ContainerContract;
use Commune\Framework\Contracts\ServiceProvider;
use Commune\Shell\Contracts\ShlServer;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $userId
 */
class RSServerServiceProvider extends ServiceProvider
{

    public static function stub(): array
    {
        return [
            'userId' => 'reactStdioUserId',

        ];
    }

    public function isProcessServiceProvider(): bool
    {
        return true;
    }

    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $app->instance(static::class, $this);

        $app->singleton(ShlServer::class, function(ContainerContract $app){

        });
    }



}