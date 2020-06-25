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
 *
 * @property-read string $host      服务地址
 * @property-read string $port         端口地址
 * @property-read bool $ssl         是否开启 ssl 加密
 * @property-read bool $reuse       端口重用
 */
class TcpServerParam extends AbsOption
{


    public static function stub(): array
    {
        return [
            'host' => '127.0.0.1',
            'port' => '12315',
            'ssl' => false,
            'reuse' => false,
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}