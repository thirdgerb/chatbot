<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Ghost\Tcp;

use Commune\Support\Option\AbsOption;
use Commune\Support\Swoole\ServerOption;
use Commune\Platform\Libs\SwlCo\TcpPlatformOption;


/**
 * Swoole Coroutine Tcp Ghost
 * Swoole 下协程 TCP 客户端实现 Ghost 的配置.
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read TcpPlatformOption $adapterOption  平台相关的配置
 * @property-read ServerOption $serverOption        服务端的配置.
 */
class SwlCoGhostOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'adapterOption' => [],
            'serverOption' => []
        ];
    }

    public static function relations(): array
    {
        return [
            'adapterOption' => TcpPlatformOption::class,
            'serverOption' => ServerOption::class,
        ];
    }


}