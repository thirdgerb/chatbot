<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Swoole;

use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $host
 * @property-read int $port
 * @property-read string $auth
 * @property-read int $dbIndex
 * @property-read int $timeout
 */
class RedisOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'host' => '127.0.0.1',
            'port' => 6379,
            'auth' => '',
            'dbIndex' => 0,
            'timeout' => 1,
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}