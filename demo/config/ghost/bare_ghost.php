<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

use Commune\Ghost\Providers as GhostProviders;
use Commune\Framework\Providers as FrameworkProviders;
use Commune\Components\Demo\DemoComponent;
use Commune\Kernel\GhostCmd;
use Commune\Ghost\Predefined\Intent\Navigation;

/**
 * 独立的 Ghost 配置. 理论上可以嵌入别的项目.
 */
return new \Commune\Ghost\IGhostConfig([

    'id' => 'bare_ghost',

    'name' => 'bare_ghost',

    'providers' => [

        /* config service */

        // 配置中心
        FrameworkProviders\OptionRegistryServiceProvider::class,

        // 多轮对话逻辑的 Mindset 模块
        GhostProviders\MindsetStorageConfigProvider::class,

        /* proc service */

        // 文件缓存.
        FrameworkProviders\FileCacheServiceProvider::class,

        // i18n 模块
        FrameworkProviders\TranslatorBySymfonyProvider::class,

        FrameworkProviders\CacheByArrProvider::class,

        // mind set
        GhostProviders\MindsetServiceProvider::class,

        // logger
        FrameworkProviders\LoggerByMonologProvider::class => [
            'name' => 'bare_ghost',

        ],

        // 基于 ConsoleLogger 的异常上报
        FrameworkProviders\ExpReporterByConsoleProvider::class,

        // sound like 模块
        FrameworkProviders\SoundLikeServiceProvider::class,
        /* req service */

        // runtime driver
        FrameworkProviders\RuntimeDriverDemoProvider::class,

        // clone service
        GhostProviders\ClonerServiceProvider::class,

        \Commune\NLU\NLUServiceProvider::class,
    ],

    'options' => [],

    'components' => [
        // 测试用例
        DemoComponent::class,
    ],

    // request protocals
    'protocals' => [
        [
            'protocal' => \Commune\Blueprint\Kernel\Protocals\GhostRequest::class,
            // interface
            'interface' => \Commune\Blueprint\Kernel\Handlers\GhostRequestHandler::class,
            // 默认的
            'default' => \Commune\Kernel\Handlers\IGhostRequestHandler::class,
        ],
    ],

    // 用户命令
    'userCommands' => [
        GhostCmd\GhostHelpCmd::class,
        GhostCmd\User\HelloCmd::class,
        GhostCmd\User\WhoCmd::class,
        GhostCmd\User\QuitCmd::class,
        GhostCmd\User\CancelCmd::class,
        GhostCmd\User\BackCmd::class,
        GhostCmd\User\RepeatCmd::class,
        GhostCmd\User\RestartCmd::class,
        GhostCmd\User\HomeCmd::class,
    ],

    // 管理员命令
    'superCommands' => [
        GhostCmd\GhostHelpCmd::class,
        GhostCmd\Super\SpyCmd::class,
        GhostCmd\Super\ScopeCmd::class,
        GhostCmd\Super\ProcessCmd::class,
        GhostCmd\Super\IntentCmd::class,
        GhostCmd\Super\RedirectCmd::class,
        GhostCmd\Super\SceneCmd::class,
        GhostCmd\Super\WhereCmd::class,
    ],

    'comprehendPipes' => [
        // NLUService::class,
    ],

    'mindPsr4Registers' => [
    ],

    // session
    'sessionExpire' => 3600,
    'sessionLockerExpire' => 3,
    'maxRedirectTimes' => 255,
    'maxRequestFailTimes' => 3,
    'mindsetCacheExpire' => 600,
    'maxBacktrace' => 3,
    'defaultContextName' => \Commune\Components\Demo\Contexts\DemoHome::genUcl()->encode(),
    'sceneContextNames' => [
    ],
    'defaultHeedFallback' =>[
    ],

    'globalContextRoutes' => [
        // 其实也可以用 __name(), 但下面这个方法才最本质.
        Navigation\CancelInt::genUcl()->encode(),
        Navigation\RepeatInt::genUcl()->encode(),
        Navigation\QuitInt::genUcl()->encode(),
        Navigation\HomeInt::genUcl()->encode(),
        Navigation\BackwardInt::genUcl()->encode(),
        Navigation\RestartInt::genUcl()->encode(),
        Navigation\WrongInt::genUcl()->encode(),
    ]

]);

