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
use Commune\Platform\Shell\Tcp;
use Commune\Support\Utils\TypeUtils;


/**
 * 接受所有消息广播的 shell
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TcpBroadcastShellPlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [
            'id' => '',
            'concrete' => Tcp\SwlBroadcastShellPlatform::class,
            'bootShell' => null,
            'bootGhost' => false,
            'providers' => [

            ],
            'options' => [
                Tcp\SwlDuplexShellOption::class => [
                    'adapterName' => Tcp\SwlBroadcastAdapter::class,
                    'tableSize' => 10000,
                    'serverOption' => [
                        'host' => '127.0.0.1',
                        'port' => '9504',
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