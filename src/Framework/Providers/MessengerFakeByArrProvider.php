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
use Commune\Contracts\Messenger\Broadcaster;
use Commune\Contracts\Messenger\MessageDB;
use Commune\Contracts\Messenger\ShellMessenger;
use Commune\Contracts\ServiceProvider;
use Commune\Framework\MessageDB\ArrMessageDB;
use Commune\Framework\Messenger\Fake\EmptyBroadcaster;
use Commune\Framework\Messenger\Fake\LocalShellMessenger;


/**
 * 假的 messenger 相关功能. 用于测试.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MessengerFakeByArrProvider extends ServiceProvider
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
            ShellMessenger::class,
            LocalShellMessenger::class
        );

        $app->singleton(
            MessageDB::class,
            ArrMessageDB::class
        );

        $app->singleton(
            Broadcaster::class,
            EmptyBroadcaster::class
        );
    }


}