<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\SwooleCo\TcpGhost;

use Commune\Platform\SwooleCo\Supports\CoProcPoolOption;
use Commune\Platform\SwooleCo\Supports\CoTcpAdapterOption;
use Commune\Support\Option\AbsOption;


/**
 * Swoole Coroutine Tcp Ghost
 * Swoole 下协程 TCP 客户端实现 Ghost 的配置.
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read CoTcpAdapterOption $adapterOption
 * @property-read CoProcPoolOption $poolOption
 */
class SCTGhostOption extends AbsOption
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
            'adapterOption' => CoTcpAdapterOption::class,
            'poolOption' => CoProcPoolOption::class,
        ];
    }


}