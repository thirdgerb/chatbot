<?php


use Commune\Platform\Libs;
use Commune\Blueprint\CommuneEnv;
use Commune\Host\IHost;
use Commune\Framework;
use Commune\Ghost\Providers as GhostProviders;


require __DIR__ . '/vendor/autoload.php';

/*
 * Commune Chatbot 命令行版的 demo.
 * 目标是无依赖启动, 可以用于开发与测试.
 */

// 设置参数

// 设置 debug 参数
CommuneEnv::defineDebug(in_array('-d', $argv));

// 设置 reset registry 参数. 这个参数会重置所有的 OptionRegistry
CommuneEnv::defineResetMind(in_array('-r', $argv));

// 设置 loading 参数.
CommuneEnv::defineLoadingResource(in_array('-l', $argv));

// 定义核心路径
CommuneEnv::defineBathPath(__DIR__ . '/demo');
CommuneEnv::defineResourcePath(__DIR__ . '/demo/resources');
CommuneEnv::defineRuntimePath(__DIR__. '/demo/runtime');


// 定义配置
/**
 * @var \Commune\Blueprint\Configs\GhostConfig $ghostConfig
 */
$ghostConfig = include __DIR__ . '/demo/config/ghost/demo.php';

// 可以写死 ghost 启动时默认加载的根语境  root context
if ($ghostConfig instanceof ArrayAccess) {

    $ghostConfig->offsetSet(
        'defaultContextName',

        // 更改这个字符串, 可以定义自己的语境.
        \Commune\Components\Demo\Contexts\DemoHome::genUcl()
    );
}

$config = new \Commune\Host\IHostConfig([

    'id' => 'demo',

    'name' => 'demo',

    'providers' => [

        /* config services */

        // 注册配置中心
        Framework\Providers\OptionRegistryServiceProvider::class,

        // 注册基于数组模拟的 cache
        Framework\Providers\CacheByArrProvider::class,

        // 注册文件缓存.
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

        // 将 messenger 全部用 数组的 faker 来替代
        Framework\Providers\MessengerFakeByArrProvider::class,

        /* req services */
        // 完全基于缓存, 无法获取长期存储的消息数据库.
        Framework\Providers\MessageDBCacheOnlyProvider::class,



    ],

    // ghost 的配置
    'ghost' => $ghostConfig,

    // shell 的配置
    'shells' => [
        'demo_shell' => new \Commune\Host\Prototype\ShellProtoConfig([
            'id' => 'demo_shell',
            'name' => 'DemoShell',
            'desc' => '测试用的 shell',
        ]),
    ],

    // 平台的配置.
    'platforms' => [
        // 命令行
        'console' => include __DIR__ . '/demo/config/platforms/console.php',
    ],


]);


$host = new IHost($config);
$host->run('console');



