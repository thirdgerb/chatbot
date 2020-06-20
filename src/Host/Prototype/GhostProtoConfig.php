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

use Commune\Blueprint\Kernel\Handlers\GhostRequestHandler;
use Commune\Framework;
use Commune\Components;
use Commune\Kernel\GhostCmd;
use Commune\Ghost\IGhostConfig;
use Commune\Kernel\GhostCmd\GhostHelpCmd;
use Commune\Ghost\Providers as GhostProviders;
use Commune\Components\Predefined\Intent\Navigation;
use Commune\Kernel\Handlers\IGhostRequestHandler;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostProtoConfig extends IGhostConfig
{

    public static function stub(): array
    {
        return [
            'id' => '',

            'name' => '',

            'providers' => [

                /* proc service */

                // mind set
                GhostProviders\MindsetServiceProvider::class,

                /* req service */

                // runtime driver
                Framework\Providers\RuntimeDriverDemoProvider::class,

                // clone service
                GhostProviders\ClonerServiceProvider::class,
            ],

            'options' => [],

            'components' => [
                // 测试用例
                Components\Demo\DemoComponent::class,
            ],

            // request protocals
            'protocals' => [
                [
                    'protocal' => GhostRequest::class,
                    // interface
                    'interface' => GhostRequestHandler::class,
                    // 默认的
                    'default' => IGhostRequestHandler::class,
                ],
            ],

            // 用户命令
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

            // 管理员命令
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