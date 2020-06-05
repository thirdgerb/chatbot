<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host\Ghost\Stdio;

use Commune\Blueprint\Ghost\Auth\Supervise;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;


/**
 * 请求级服务.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioReqServiceProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [];
    }

    public function boot(ContainerContract $app): void
    {
    }


    public function getDefaultScope(): string
    {
        return self::SCOPE_REQ;
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(Supervise::class, SGSupervise::class);
    }


}