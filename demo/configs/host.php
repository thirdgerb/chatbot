<?php


use Commune\Framework;
use Commune\Host\IHostConfig;
use Commune\Ghost\Providers as GhostProviders;


return new IHostConfig([

    'id' => 'demo',

    'name' => 'demo',

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

    // ghost 的配置
    // 监听端口 9501
    'ghost' => include __DIR__ . '/ghost/tcp_co_ghost.php',

    // shell 的配置
    'shells' => [
        // demo shell
        'demo_shell' => include __DIR__ . '/shells/demo_shell.php',
    ],

    // 平台的配置.
    'platforms' => [

        // ghost 端, 监听 9501 端口
        'ghost' => include __DIR__ . '/platforms/ghost.php',

        // 同步 shell 端, 监听 9502 端口.
        'sync_shell' => include __DIR__ . '/platforms/sync_shell.php',
    ],

]);
