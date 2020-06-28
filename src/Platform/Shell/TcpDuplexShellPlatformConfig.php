<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Shell;

use Commune\Platform\IPlatformConfig;
use Commune\Platform\Shell\Tcp\SwlDuplexShellOption;
use Commune\Platform\Shell\Tcp\SwlDuplexShellPlatform;
use Commune\Platform\Shell\Tcp\SwlDuplexTextShellAdapter;
use Commune\Support\Utils\TypeUtils;


/**
 * 基于 Swoole 异步风格的 Tcp 服务端实现的 Shell Platform.
 *
 * 通过子进程监听 Broadcaster, 从而能够实现 同步 + 异步 的消息, 通过双工通道主动推送给用户.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TcpDuplexShellPlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [
            'id' => '',
            'concrete' => SwlDuplexShellPlatform::class,
            'bootShell' => null,
            'bootGhost' => false,
            'providers' => [

            ],
            'options' => [
                SwlDuplexShellOption::class => [
                    'adapterName' => SwlDuplexTextShellAdapter::class,
                    'tableSize' => 10000,
                    'serverOption' => [
                        'host' => '127.0.0.1',
                        'port' => '9503',

                    ],
                ],
            ],
        ];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        return TypeUtils::requireFields($data, ['bootShell'])
            ?? parent::validate($data);
    }

}