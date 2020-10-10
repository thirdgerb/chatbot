<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint;

use Commune\Blueprint\Ghost\Mindset;
use Commune\Contracts;
use Commune\Protocols;
use Commune\Blueprint\Framework;
use Commune\Blueprint\Configs;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\SoundLike\SoundLikeInterface;
use Psr\Log\LoggerInterface;

/**
 * 系统启动时默认的功能模块定义.
 * 可以用于启动自检.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DependInjections
{
    /*------- 所有 App 启动时都必须有的 -------*/

    // App 启动前的基础绑定.
    const BASIC_APP_BINDINGS = [
        // 服务的注册中心
        Framework\ServiceRegistry::class,
        // console 日志
        Contracts\Log\ConsoleLogger::class,
        // 系统日志的格式定义.
        Contracts\Log\LogInfo::class,
    ];

    // App 的进程级绑定
    const APP_PROC_BINDINGS = [
        // 进程级容器
        Framework\ProcContainer::class,
        // 异常报告
        Contracts\Log\ExceptionReporter::class,
        // 注册表
        OptRegistry::class,
        // 文本翻译模块
        Contracts\Trans\Translator::class,
        // 日志模块
        LoggerInterface::class,
        // 缓存模块
        Contracts\Cache::class,
        // 权限管理
        Framework\Auth\Authority::class,
    ];

    /*------- host -------*/

    const HOST_PROC_BINDINGS = [
        // Host 自身.
        Host::class,
        // host 的配置.
        Configs\HostConfig::class,
    ];

    /*------- ghost -------*/

    // ghost 的进程级绑定
    const GHOST_PROC_BINDINGS = [
        // ghost 本体
        Ghost::class,
        // ghost 的配置
        Configs\GhostConfig::class,
        // 思维模块, 注册所有多轮对话逻辑
        Mindset::class,
        // 拼音模块, 用于拼音校验.
        SoundLikeInterface::class,
    ];

    // ghost 的请求级绑定.
    const GHOST_REQ_BINDINGS = [
        Ghost\Cloner\ClonerScene::class,
        Ghost\Cloner\ClonerScope::class,
        Ghost\Cloner\ClonerLogger::class,
        Ghost\Cloner\ClonerStorage::class,
        Ghost\Cloner\ClonerAvatar::class,
        Ghost\Cloner\ClonerStorage::class,
        Ghost\Cloner\ClonerDispatcher::class,

        Ghost\Tools\Matcher::class,

        Ghost\Runtime\Runtime::class,
        Contracts\Ghost\RuntimeDriver::class,
        // 广播模块
        Contracts\Messenger\Broadcaster::class,
        // ghost 自己发送异步消息
        Contracts\Messenger\GhostMessenger::class,
    ];

    // ghost 请求创建时绑定的实例.
    const GHOST_REQ_INSTANCES = [
        Ghost\Cloner::class,
        Framework\ReqContainer::class,
        Protocols\Intercom\InputMsg::class,
        Protocols\Comprehension::class,
    ];

    /*------- shell -------*/

    const SHELL_PROC_BINDINGS = [
        Shell::class,
        Configs\ShellConfig::class,
    ];

    const SHELL_REQ_BINDINGS = [
        Shell::class,
        Shell\Session\ShellLogger::class,
        Shell\Session\ShellStorage::class,
        // 发送消息给 ghost
        Contracts\Messenger\ShellMessenger::class,
        Contracts\Messenger\Broadcaster::class,
    ];

    const SHELL_REQ_INSTANCES = [
        Shell\ShellSession::class,
        Framework\ReqContainer::class,
        Protocols\Intercom\InputMsg::class,
        Protocols\Comprehension::class,
    ];
}