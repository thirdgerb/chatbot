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
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\LogInfo;

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
     * @return ProcContainer
     */
    public function getProcContainer() : ProcContainer;

    /**
     * @return ReqContainer
     */
    public function getBasicReqContainer() : ReqContainer;

    /**
     * 同时在进程和请求级容器中绑定一个单例.
     *
     * @param $abstract
     * @param $instance
     */
    public function instance($abstract, $instance) : void;

    /*------ services ------*/

    /**
     * 服务注册中心.
     * @return ServiceRegistry
     */
    public function getServiceRegistry() : ServiceRegistry;

    /*------ logger ------*/

    /**
     * @return ConsoleLogger
     */
    public function getConsoleLogger() : ConsoleLogger;

    /**
     * @return LogInfo
     */
    public function getLogInfo() : LogInfo;



}