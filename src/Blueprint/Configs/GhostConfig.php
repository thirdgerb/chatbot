<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Configs;

use Commune\Framework;
use Commune\Blueprint\Configs\Nest\ProtocalOption;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Ghost\Handlers\GhostRequestHandler;
use Commune\Support\Option\AbsOption;
use Commune\Ghost\Providers as GhostProviders;
use Commune\Components;

/**
 * 机器人多轮对话内核的配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $id                    Ghost 的 Id, 必须纯字母
 * @property-read string $name                  Ghost 的名称. 任意表达
 *
 * ## 服务注册
 *
 * @property-read array $configProviders        需要绑定的配置服务.
 * @property-read array $procProviders          需要绑定的进程级服务.
 *  [   ServiceProvider::class,
 *      ServiceProvider1::class => [ configs ]]
 *
 * @property-read array $reqProviders           需要绑定的请求级服务
 *
 * ## 组件注册
 * @property-read array $components             默认绑定的组件.
 *  [   ComponentClass::class,
 *      ComponentClass1::class => [configs] ]
 *
 * ## 配置注册
 * @property-read array $options                默认绑定的 Option 单例
 *  [   OptionClass::class,
 *      OptionClass1::class => [ configs ] ]
 *
 * ## 管道配置
 *
 * @property-read string[] $apiHandlers         api消息的处理器.
 * @property-read string[] $userCommands
 * @property-read string[] $superCommands
 * @property-read string[] $comprehensionPipes  对输入信息进行抽象理解的管道.
 *
 * ## Session 配置
 *
 * @property-read int $sessionExpire            Session 的过期时间, 秒
 * @property-read int $sessionLockerExpire      Session 锁的过期时间, 为0 则不锁
 * @property-read int $maxRedirectTimes
 * @property-read int $maxRequestFailTimes
 * @property-read int $mindsetCacheExpire       Mindset 的配置过期时间.
 * @property-read int $maxBacktrace
 * @property-read string|null $confuseHandler
 *
 * @property-read Nest\ProtocalOption[] $protocals   Session 可以处理的协议.
 * [
 *     [
 *         'group' => 'name',
 *         'protocal' => Protocal::class,
 *         'handler' => Handler::class,
 *         'params' => [ param1, param2...]
 *     ],
 * ]
 *
 *
 *
 * # 多轮对话相关逻辑.
 *
 * @property-read string[] $sceneContextNames   场景对应的根路径.
 */
class GhostConfig extends AbsOption
{
    const IDENTITY = 'id';

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
                Framework\Providers\SpyAgencyServiceProvider::class,
                GhostProviders\MindsetServiceProvider::class,
            ],
            'reqProviders' => [
                Framework\Providers\CacheByArrProvider::class,
                Framework\Providers\RuntimeDriverDemoProvider::class,
                GhostProviders\ClonerServiceProvider::class,
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
            'apiHandler' => [

            ],
            'comprehensionPipes' => [

            ],
            'userCommands' => [

            ],

            // session
            'sessionExpire' => 3600,
            'sessionLockerExpire' => 3,
            'maxRedirectTimes' => 255,
            'mindsetCacheExpire' => 600,
            'maxBacktrace' => 10,
            'sceneContextNames' => [
            ],
        ];
    }

    public static function relations(): array
    {
        return [
            'protocals[]' => ProtocalOption::class,
        ];
    }

}