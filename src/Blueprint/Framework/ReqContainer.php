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
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ReqContainer extends ContainerContract
{
    /**
     * 请求容器的 ID. 理论上是唯一 ID
     * @return string
     */
    public function getUuid() : string;

    /**
     * @return ContainerContract
     */
    public function getProcessContainer() : ContainerContract;

    /**
     * 是否实例化了.
     * @return bool
     */
    public function isInstanced() : bool;

    /**
     * 获取实例, 并且进行初始化.
     *
     * ReqContainer 会用一个公共的实例来绑定各种工厂, 但每个请求需要再生成一个实例.
     *
     * @param string $id;
     * @param ContainerContract $procContainer
     * @return static
     */
    public function newInstance(string $id, ContainerContract $procContainer) : ReqContainer;

    /**
     * 请求级容器进行回收, 避免相互持有导致内存溢出.
     */
    public function destroy() : void;

}