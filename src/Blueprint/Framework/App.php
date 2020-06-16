<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework;

use Commune\Blueprint\Exceptions\CommuneBootingException;
use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\LogInfo;
use Commune\Support\Protocal\Protocal;
use Commune\Support\Protocal\ProtocalMatcher;

/**
 * 基于双容器策略的基本框架.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface App
{
    /**
     * App 的唯一 ID 标识, 可用于各种缓存和数据存储.
     * @return string
     */
    public function getId() : string;

    /**
     * App 的名称. 可以对外展示的.
     * @return string
     */
    public function getName() : string;

    /*------ run ------*/

    /**
     * 指定启动错误时的响应逻辑. 默认是 exit(1)
     *
     * @param callable $fail
     * @return App
     */
    public function onFail(callable $fail) : App;

    /**
     * 应用初始化. 主要是注册各种服务.
     * @return static
     */
    public function bootstrap() : App;

    /**
     * 激活应用, 注册所有的 Components, 然后启动 (boot) 所有进程级容器的服务.
     * 正常启动流程:
     *
     * $app->bootstrap()->activate();
     *
     * @return static
     * @throws CommuneBootingException
     */
    public function activate() : App;


    /**
     * 创建一个请求级容器
     * 并添加 ReqContainer 的单例绑定到容器自身.
     *
     * @param string $uuid
     * @return ReqContainer
     */
    public function newReqContainerIns(string $uuid) : ReqContainer;

    /*------ container ------*/

    /**
     * @return ContainerContract
     */
    public function getProcContainer() : ContainerContract;

    /**
     * @return ReqContainer
     */
    public function getBasicReqContainer() : ReqContainer;

    /*------ services ------*/

    /**
     * 服务注册中心.
     * @return ServiceRegistrar
     */
    public function getServiceRegistrar() : ServiceRegistrar;

    /*------ logger ------*/

    /**
     * @return ConsoleLogger
     */
    public function getConsoleLogger() : ConsoleLogger;

    /**
     * @return LogInfo
     */
    public function getLogInfo() : LogInfo;


    /*------ request ------*/

    /**
     * @param AppRequest $request
     * @return AppResponse
     */
    public function handleRequest(AppRequest $request) : AppResponse;

    /*------ protocal ------*/

    /**
     * 协议的匹配器.
     * @return ProtocalMatcher
     */
    public function getProtocalMatcher() : ProtocalMatcher;

    /**
     * 遍历定义的协议, 轮流获取可能的 handler
     *
     * 建议所有的协议 handler 都应该是一个 callable 对象.
     *
     * @param ReqContainer $container
     * @param Protocal $protocal
     * @param string|null $handlerInterface
     * @return \Generator  $handlerInterface[]
     */
    public function eachProtocalHandler(
        ReqContainer $container,
        Protocal $protocal,
        string $handlerInterface = null
    ) : \Generator;

}