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
use Commune\Platform\Libs\SwlCo\TcpPlatformOption;
use Commune\Platform\Ghost\Tcp\SwlCoBabelGhostAdapter;
use Commune\Support\Swoole\ServerOption;


/**
 * Swoole Tcp 协程实现的 Ghost 服务端的基础配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TcpCoGhostPlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [
            'id' => '',
            'name' => '',
            'desc' => '',
            'concrete' => SwlCoGhostPlatform::class,
            // Ghost 服务端不启动 shell
            'bootShell' => null,
            // 启动 Ghost
            'bootGhost' => true,
            'providers' => [
                // 基于 Channel 实现的 Ghost 端异步投递消息.
                GhtMessengerBySwlChanProvider::class => [
                    'chanCapacity' => 1000,
                    'chanTimeout' => 0.1,
                ],
            ],
            'options' => [
                SwlCoGhostOption::class => [
                    /**
                     * @see ServerOption
                     */
                    'serverOption' => [
                        'host' => '127.0.0.1',
                        'port' => '9501',
                        'serverSettings' => [
                            'open_eof_split' => true, // 启用 EOF 自动分包
                            'package_eof' => "\r\n",
                        ],
                    ],
                    /**
                     * @see TcpPlatformOption
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