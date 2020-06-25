<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Ghost\TcpCo;

use Commune\Support\Option\AbsOption;
use Commune\Support\Swoole as SwooleSupport;


/**
 * Tcp - Coroutine - Ghost - Platform 的服务端配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * ## 进程配置
 *
 * @property-read int $workerNum          Worker 进程数
 *
 * ## Server 配置
 *
 * @property-read string $host
 * @property-read string $port
 * @property-read bool $ssl
 * @property-read array $serverOption
 *
 *
 * ## chan
 * @property-read int $chanCapacity
 * @property-read int $chanTimeout
 * @property-read int $chanNum
 */
class TCGServerOption extends AbsOption
{

    public static function stub(): array
    {
        return [
            'workerNum' => 2,

            'host' => '127.0.0.1',
            'port' => '9501',
            'ssl' => false,

            'serverOption' => [],

            // chan
            'chanCapacity' => 100,
            'chanTimeout' => 0.1,
            'chanNum' => 1
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}