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

use Commune\Container\ContainerContract;

/**
 * 基于双容器策略的基本框架.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface App
{
    /**
     * 是否调试状态.
     * @return bool
     */
    public function isDebugging() : bool;

    /**
     * 创建一个请求级容器
     * 并添加 ReqContainer 的单例绑定到容器自身.
     *
     * @param string $uuid
     * @return ReqContainer
     */
    public function newReqContainerInstance(string $uuid) : ReqContainer;

    /*------ container ------*/

    /**
     * @return ContainerContract
     */
    public function getProcContainer() : ContainerContract;

    /**
     * @return ContainerContract
     */
    public function getReqContainer() : ContainerContract;

    /*------ registrar ------*/

    /**
     * 服务注册中心.
     * @return ServiceRegistrar
     */
    public function getServiceRegistrar() : ServiceRegistrar;
}