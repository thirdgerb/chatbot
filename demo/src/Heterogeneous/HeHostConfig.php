<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Demo\Heterogeneous;

use Commune\Framework;
use Commune\Ghost\Providers as GhostProviders;
use Commune\Host\Prototype\HostProtoConfig;
use Commune\Platform\Ghost\TcpCo\TCGPlatformConfig;



/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HeHostConfig extends HostProtoConfig
{

    public static function stub(): array
    {
        return [
            'id' => '',
            'name' => '',
            'providers' => [

                /* config services */

                // 配置中心
                Framework\Providers\OptRegistryProvider::class,

                // mind 配置
                GhostProviders\MindsetStorageConfigProvider::class,

                // i18n 模块
                Framework\Providers\TranslatorBySymfonyProvider::class,

                // 文件缓存.
                Framework\Providers\FileCacheServiceProvider::class,

                /* proc services */


                // mind set
                GhostProviders\MindsetServiceProvider::class,

                // exception reporter
                Framework\Providers\ExpReporterByConsoleProvider::class,
                // logger
                Framework\Providers\LoggerByMonologProvider::class,
                // sound like 模块
                Framework\Providers\SoundLikeServiceProvider::class,

                // messenger
                Framework\Providers\MessengerFakeByArrProvider::class,

                /* req services */
                Framework\Providers\CacheByArrProvider::class,
            ],
            'options' => [],
            'ghost' => [],
            'shells' => [],
            'platforms' => [
                // stdio 本地 platform

                // Tcp Ghost platform
                'ghost' => new TCGPlatformConfig([
                    'id' => 'ghost',
                ]),

                // Tcp 双工 shell
                'syncTcpShell' => [],


                // Websocket Shell
                // Http Api Shell
            ],
        ];

    }


}