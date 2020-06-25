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
use Commune\Platform\Libs\SwlCo\TcpAdapterOption;


/**
 * Swoole Coroutine Tcp Ghost
 * Swoole 下协程 TCP 客户端实现 Ghost 的配置.
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read TcpAdapterOption $adapterOption
 * @property-read ServerOption $poolOption
 */
class SwlCoGhostOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'adapterOption' => [],
            'poolOption' => []
        ];
    }

    public static function relations(): array
    {
        return [
            'adapterOption' => TcpAdapterOption::class,
            'poolOption' => ServerOption::class,
        ];
    }


}