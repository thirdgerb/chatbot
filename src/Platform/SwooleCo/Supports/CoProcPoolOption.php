<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\SwooleCo\Supports;

use Commune\Support\Option\AbsOption;


/**
 * 协程进程池的配置.
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
 * @property-read float $sendTimeout      发送响应的过期时间.
 *
 */
class CoProcPoolOption extends AbsOption
{

    public static function stub(): array
    {
        return [
            'workerNum' => 2,

            'host' => '127.0.0.1',
            'port' => '9501',
            'ssl' => false,

            'serverOption' => [],

            'sendTimeout' => 0.3,
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}