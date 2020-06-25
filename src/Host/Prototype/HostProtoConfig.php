<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host\Prototype;

use Commune\Framework;
use Commune\Host\IHostConfig;
use Commune\Ghost\Providers as GhostProviders;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HostProtoConfig extends IHostConfig
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

                /* proc services */

                // 文件缓存.
                Framework\Providers\FileCacheServiceProvider::class,
                // i18n 模块
                Framework\Providers\TranslatorBySymfonyProvider::class,

                // mind set
                GhostProviders\MindsetStorageConfigProvider::class,

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
            'platforms' => [],
        ];
    }

}