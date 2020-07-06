<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Libs\SwlCo;

use Commune\Support\Option\AbsOption;
use Commune\Support\Utils\TypeUtils;

/**
 * Swoole Coroutine TCP 服务端的基础配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $tcpAdapter        Swoole Tcp 接口的适配器.
 * @property-read float $receiveTimeout     每一个 Tcp Connection 传送消息的过期时间. 默认是 0
 */
class TcpPlatformOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'tcpAdapter' => '',
            'receiveTimeout' => 0
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {

        return TypeUtils::requireFields($data, ['tcpAdapter'])
            ?? parent::validate($data);
    }

}