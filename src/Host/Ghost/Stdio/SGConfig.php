<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host\Ghost\Stdio;

use Commune\Blueprint\Ghost\Handlers\GhtRequestHandler;
use Commune\Support\Protocal\ProtocalOption;
use Commune\Framework;
use Commune\Components;
use Commune\Ghost\Commands;
use Commune\Ghost\Cmd\GhostHelpCmd;
use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Kernel\Protocals\CloneRequest;
use Commune\Ghost\Handlers\GhostRequestHandler;
use Commune\Ghost\Providers as GhostProviders;
use Commune\Components\Predefined\Intent\Navigation;
use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SGConfig extends AbsOption implements GhostConfig
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => 'demo',
            'name' => 'demo',

            'configProviders' => [
                Framework\Providers\OptRegistryProvider::class,
                Framework\Providers\FileCacheServiceProvider::class,
                GhostProviders\MindsetStorageConfigProvider::class,
                Framework\Providers\TranslatorBySymfonyProvider::class,
            ],
            'procProviders' => [
                Framework\Providers\ExpReporterByConsoleProvider::class,
                Framework\Providers\LoggerByMonologProvider::class,
                Framework\Providers\SoundLikeServiceProvider::class,
                GhostProviders\MindsetServiceProvider::class,
            ],
            'reqProviders' => [
                Framework\Providers\CacheByArrProvider::class,
                Framework\Providers\RuntimeDriverDemoProvider::class,
                GhostProviders\ClonerServiceProvider::class,
                StdioReqServiceProvider::class,
            ],
            'components' => [
                // 测试用例
                Components\Demo\DemoComponent::class,
            ],
            'options' => [
            ],

            'protocals' => [

                // request protocals
                [
                    'protocal' => CloneRequest::class,
                    'interface' => GhtRequestHandler::class,
                    'handlers' => [
                        [ 'handler' => GhostRequestHandler::class],
                    ]
                ]
            ],

            'userCommands' => [
                GhostHelpCmd::class,
                Commands\User\HelloCmd::class,
                Commands\User\WhoCmd::class,
                Commands\User\QuitCmd::class,
                Commands\User\CancelCmd::class,
                Commands\User\BackCmd::class,
                Commands\User\RepeatCmd::class,
                Commands\User\RestartCmd::class,
            ],
            'superCommands' => [
                GhostHelpCmd::class,
                Commands\Super\SpyCmd::class,
                Commands\Super\ScopeCmd::class,
                Commands\Super\ProcessCmd::class,
                Commands\Super\IntentCmd::class,
                Commands\Super\RedirectCmd::class,
            ],
            'comprehensionPipes' => [

            ],
            'mindPsr4Registers' => [
                "Commune\\Host\\Ghost\\Stdio\\Context" => __DIR__ . '/Context'

            ],
            // session
            'sessionExpire' => 3600,
            'sessionLockerExpire' => 3,
            'maxRedirectTimes' => 255,
            'mindsetCacheExpire' => 600,
            'maxBacktrace' => 3,
            'defaultContextName' => Components\Demo\Contexts\DemoHome::genUcl()->encode(),
            'sceneContextNames' => [
            ],
            'globalContextRoutes' => [
                Navigation\CancelInt::genUcl(),
                Navigation\RepeatInt::genUcl(),
                Navigation\QuitInt::genUcl(),
                Navigation\HomeInt::genUcl(),
                Navigation\BackwardInt::genUcl(),
                Navigation\RestartInt::genUcl(),
            ]
        ];
    }

    public static function relations(): array
    {
        return [
            'requestHandlers[]' => ProtocalOption::class,
            'apiHandlers[]' => ProtocalOption::class
        ];
    }


}