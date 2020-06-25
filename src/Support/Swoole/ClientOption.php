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
use Swoole\ConnectionPool;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $host
 * @property-read string $port
 * @property-read float $connectTimeout
 * @property-read float $receiveTimeout
 * @property-read int $poolSize
 */
class ClientOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'host' => '',
            'port' => '',
            'connectTimeout' => 0.3,
            'receiveTimeout' => 0.3,
            'poolSize' => ConnectionPool::DEFAULT_SIZE
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}