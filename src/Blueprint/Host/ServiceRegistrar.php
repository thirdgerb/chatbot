<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Host;

use Commune\Blueprint\Exceptions\HostBootingException;
use Commune\Framework\Contracts\ServiceProvider;


/**
 * Host 各种服务的注册中心.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ServiceRegistrar
{
    /*----------- 服务注册 -----------*/

    /**
     * 注册配置相关的服务, 优先级最高.
     * @param ServiceProvider $provider
     * @param bool $top
     */
    public function registerConfigProvider(
        ServiceProvider $provider,
        bool $top
    ) : void;


    /**
     * 注册进程级的服务.
     * @param ServiceProvider $provider
     * @param bool $top
     */
    public function registerProcProvider(
        ServiceProvider $provider,
        bool $top
    ) : void;

    /**
     * 注册请求级服务.
     * @param ServiceProvider $provider
     * @param bool $top
     */
    public function registerReqProvider(
        ServiceProvider $provider,
        bool $top
    ) : void;

    /*----------- 获取服务 -----------*/

    /**
     * @return ServiceProvider[] string => ServiceProvider
     */
    public function getConfigProviders() : array;

    /**
     * @return ServiceProvider[] string => ServiceProvider
     */
    public function getProcProviders() : array;

    /**
     * @return ServiceProvider[] string => ServiceProvider
     */
    public function getReqProviders() : array;

    /*----------- 初始化服务 -----------*/

    /**
     * 启动配置类服务.
     * @return bool
     * @throws HostBootingException
     */
    public function bootConfigServices() : bool;

    /**
     * 启动进程类服务
     * @return bool
     * @throws HostBootingException
     */
    public function bootProcServices() : bool;

    /**
     * 启动请求类服务
     * @return bool
     * @throws HostBootingException
     */
    public function bootReqServices() : bool;

    /*----------- 状态 -----------*/

    /**
     * @return bool
     */
    public function isConfigServicesBooted() : bool;

    /**
     * @return bool
     */
    public function isReqServicesBooted() : bool;

    /**
     * @return bool
     */
    public function isProcServicesBooted() : bool;

}