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

use Commune\Framework;
use Commune\Components;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Ghost\ClonePipes;
use Commune\Ghost\Handlers\GhostRequestHandler;
use Commune\Ghost\Providers as GhostProviders;



/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SGConfig extends GhostConfig
{
    public static function stub(): array
    {
        return [
            'id' => 'demo',
            'name' => 'demo',

            'configProviders' => [
                Framework\Providers\OptRegistryProvider::class,
                Framework\FileCache\FileCacheServiceProvider::class,
                GhostProviders\MindsetStorageConfigProvider::class,
            ],
            'procProviders' => [
                Framework\Providers\ExpReporterByConsoleProvider::class,
                Framework\Providers\LoggerByMonologProvider::class,
                GhostProviders\MindsetServiceProvider::class,
            ],
            'reqProviders' => [
                Framework\Providers\CacheByArrProvider::class,
                Framework\Providers\RuntimeDriverDemoProvider::class,
                GhostProviders\GhostReqServiceProvider::class,
            ],
            'components' => [
                Components\Predefined\PredefinedComponent::class,
                Components\Demo\DemoComponent::class,
            ],
            'options' => [
            ],
            // protocals
            'protocals' => [
                [
                    'group' => Session::PROTOCAL_GROUP_REQUEST,
                    'protocal' => GhostRequest::class,
                    'handler' => GhostRequestHandler::class,
                ]
            ],
            // pipeline
            'clonePipes' => [
                ClonePipes\CloneInputPipe::class,

            ],
            'comprehensionPipes' => [

            ],
            // session
            'sessionExpire' => 3600,
            'sessionLockerExpire' => 3,
            'maxRedirectTimes' => 255,
            'mindsetCacheExpire' => 600,
            'sceneContextNames' => [
            ],
            'defaultScene' => '',

        ];
    }

}