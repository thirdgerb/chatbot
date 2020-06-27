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
        Framework\Providers\OptRegistryByFileProvider::class,

        // Redis 连接池.
        Framework\Providers\RedisPoolBySwooleProvider::class,

        // 基于 Redis 连接池的缓存
        Framework\Providers\CacheByRedisPoolProvider::class,

        /* proc services */

        // 文件缓存.
        Framework\Providers\FileCacheServiceProvider::class,

        // i18n 模块
        Framework\Providers\TranslatorBySymfonyProvider::class,

        // 多轮对话逻辑的 Mindset 模块
        GhostProviders\MindsetStorageConfigProvider::class,

        // 基于 ConsoleLogger 的异常上报
        Framework\Providers\ExpReporterByConsoleProvider::class,

        // 基于 monolog 实现的日志.
        Framework\Providers\LoggerByMonologProvider::class,

        // sound like 模块
        Framework\Providers\SoundLikeServiceProvider::class,

        // 假的消息发送器.
        // Framework\Providers\MessengerFakeByArrProvider::class,

        // 基于 redis 连接池实现的消息广播
        Framework\Providers\BroadcasterBySwlRedisProvider::class => [
            'listeningShells' => ['listener_shell'],
        ],

        // shell 向 Ghost 通信的工具.
        Framework\Providers\ShlMessengerBySwlCoTcpProvider::class => [
            'ghostHost' => '127.0.0.1',
            'ghostPort' => '9501',
        ],

        /* req services */

        // 完全基于缓存, 无法获取长期存储的消息数据库.
        Framework\Providers\MessageDBCacheOnlyProvider::class,

        // 用数组实现的缓存.
        // Framework\Providers\CacheByArrProvider::class,


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

        // 基于 Stdio 实现的单点对话机器人.
        'stdio_shell' => include __DIR__ . '/platforms/stdio_shell.php',

        // ghost 端, 监听 9501 端口
        'ghost' => include __DIR__ . '/platforms/ghost.php',

        // 同步 shell 端, 监听 9502 端口.
        'sync_shell' => include __DIR__ . '/platforms/sync_shell.php',

        // 双工 Shell 端, 监听 9503 端口.
        'duplex_shell' =>  include __DIR__ . '/platforms/duplex_shell.php',

        // 模拟接受广播的 shell 端, 监听 9504 端口
        'listener_shell' =>  include __DIR__ . '/platforms/listener_shell.php',

    ],

]);
