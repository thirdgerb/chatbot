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
use Commune\Framework\Providers\ShlMessengerBySwlCoTcpProvider;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TcpSyncShellPlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [
            'id' => '',
            'concrete' => Tcp\SwlCoShellPlatform::class,
            'bootShell' => null,
            'bootGhost' => true,
            'providers' => [
                ShlMessengerBySwlCoTcpProvider::class => [
                    'host' => '127.0.0.1',
                    'port' => '9501',
                ],
            ],
            'options' => [
                Tcp\SwlCoShellOption::class => [
                    'poolOption' => [
                        'workerNum' => 2,
                        'host' => '127.0.0.1',
                        'port' => '9502',
                        'ssl' => false,
                        'serverOption' => [
                        ],
                    ],
                    /**
                     * @see TcpAdapterOption
                     */
                    'adapterOption' => [
                        'tcpAdapter' => Tcp\SwlCoTextShellAdapter::class,
                        'receiveTimeout' => 0
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