<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Ghost;

use Commune\Framework\Providers\GhtMessengerBySwlChanProvider;
use Commune\Platform\Ghost\Tcp\SwlCoGhostOption;
use Commune\Platform\IPlatformConfig;
use Commune\Platform\Ghost\Tcp\SwlCoGhostPlatform;
use Commune\Platform\Libs\SwlCo\TcpAdapterOption;
use Commune\Platform\Ghost\Tcp\SwlCoBabelGhostAdapter;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TcpCoGhostPlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [
            'id' => 'demo',
            'concrete' => SwlCoGhostPlatform::class,
            'bootShell' => null,
            'bootGhost' => true,
            'providers' => [
                GhtMessengerBySwlChanProvider::class => [
                    'chanCapacity' => 1000,
                    'chanTimeout' => 0.1,
                ],
            ],
            'options' => [
                SwlCoGhostOption::class => [
                    'poolOption' => [
                        'workerNum' => 2,
                        'host' => '127.0.0.1',
                        'port' => '9501',
                        'ssl' => false,
                        'serverOption' => [
                        ],
                    ],
                    /**
                     * @see TcpAdapterOption
                     */
                    'adapterOption' => [
                        'tcpAdapter' => SwlCoBabelGhostAdapter::class,
                        'receiveTimeout' => 0
                    ],

                ],
            ],
        ];
    }
}