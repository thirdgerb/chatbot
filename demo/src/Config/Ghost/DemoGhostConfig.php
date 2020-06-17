<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Demo\Config\Ghost;

use Commune\Framework;
use Commune\Components;
use Commune\Ghost\Commands;
use Commune\Ghost\IGhostConfig;
use Commune\Ghost\Cmd\GhostHelpCmd;
use Commune\Ghost\Providers as GhostProviders;
use Commune\Components\Predefined\Intent\Navigation;
use Commune\Host\Ghost\Stdio\StdioReqServiceProvider;



/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DemoGhostConfig extends IGhostConfig
{

    public static function stub(): array
    {
        return [
            'id' => 'demo',

            'name' => 'demo',

            'providers' => [
                // config providers
                Framework\Providers\OptRegistryProvider::class,
                Framework\Providers\FileCacheServiceProvider::class,
                GhostProviders\MindsetStorageConfigProvider::class,
                Framework\Providers\TranslatorBySymfonyProvider::class,

                // proc providers
                Framework\Providers\ExpReporterByConsoleProvider::class,
                Framework\Providers\LoggerByMonologProvider::class,
                Framework\Providers\SoundLikeServiceProvider::class,
                GhostProviders\MindsetServiceProvider::class,

                // req providers
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
            // request protocals
            'protocals' => [
//                [
//                    'protocal' => CloneRequest::class,
//                    'handler' => GhostRequestHandler::class,
//                ]
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
            ],

            // session
            'sessionExpire' => 3600,
            'sessionLockerExpire' => 3,
            'maxRedirectTimes' => 255,
            'maxRequestFailTimes' => 3,
            'mindsetCacheExpire' => 600,
            'maxBacktrace' => 3,
            'defaultContextName' => Components\Demo\Contexts\DemoHome::genUcl()->encode(),
            'sceneContextNames' => [
            ],
            'globalContextRoutes' => [
                Navigation\CancelInt::genUcl()->encode(),
                Navigation\RepeatInt::genUcl()->encode(),
                Navigation\QuitInt::genUcl()->encode(),
                Navigation\HomeInt::genUcl()->encode(),
                Navigation\BackwardInt::genUcl()->encode(),
                Navigation\RestartInt::genUcl()->encode(),
            ]
        ];

    }

}