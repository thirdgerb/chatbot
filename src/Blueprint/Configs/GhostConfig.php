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

use Commune\Components;
use Commune\Support\Option\Option;
use Commune\Support\Protocal\ProtocalHandlerOpt;

/**
 * 机器人多轮对话内核的配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $id                                Ghost 的 Id, 必须纯字母+数字
 * @property-read string $name                              Ghost 的名称. 任意表达
 *
 * ## 服务注册
 *
 * @property-read array $configProviders                    需要绑定的配置服务.
 * @property-read array $procProviders                      需要绑定的进程级服务.
 * @property-read array $reqProviders                       需要绑定的请求级服务
 *
 * [   ServiceProvider::class,
 *      ServiceProvider1::class => [ configs ]]
 *
 *
 * ## 组件注册
 *
 * @property-read array $components                         默认绑定的组件.
 *
 *  [   ComponentClass::class,
 *      ComponentClass1::class => [configs] ]
 *
 * ## 配置注册
 *
 * @property-read array $options                            默认绑定的 Option 单例
 *
 *  [   OptionClass::class,
 *      OptionClass1::class => [ configs ] ]
 *
 * ## 协议定义.
 *
 * @property-read ProtocalHandlerOpt[] $requestHandlers     Ghost 处理协议的配置.
 * [
 *     [
 *         'protocal' => Protocal::class,
 *         'handler' => Handler::class,
 *         'params' => [ param1, param2...]
 *     ],
 * ]
 *
 * @property-read ProtocalHandlerOpt[] $apiHandlers         Api 消息的处理器.
 *
 * ## 管道配置
 *
 * @property-read string[] $userCommands                    用户命令
 * @property-read string[] $superCommands                   管理员命令
 *
 * ## 会话配置
 *
 * @property-read int $sessionExpire                        会话的过期时间, 秒
 * @property-read int $sessionLockerExpire                  请求锁的过期时间, 为0 则不锁
 *
 * ## 多轮对话逻辑.
 *
 * @property-read string[] $comprehensionPipes              对输入信息进行抽象理解的管道.
 * @property-read int $maxRedirectTimes                     单词对话语境重定向的最大次数
 * @property-read int $maxRequestFailTimes                  对话最大的请求异常次数, 超过了就会重启会话
 * @property-read int $mindsetCacheExpire                   Mindset 的配置过期时间.
 * @property-read string[] $mindPsr4Registers               Mind 模块自定义加载路径.
 *
 *  [namespace  => path]
 *
 * @property-read int $maxBacktrace                         可以返回上一步的最大深度.
 * @property-read string|null $confuseHandler               公共的 confuse 处理器, 处理语境无法理解的信息.
 *
 * @property-read string $defaultContextName                默认启动场景.
 * @property-read string[] $sceneContextNames               场景对应的 context
 * @property-read string[] $globalContextRoutes             全局的高优先级路由. 注意性能开销.
 */
interface GhostConfig extends Option
{

}