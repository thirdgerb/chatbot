<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint;

use Commune\Container\ContainerContract;

/**
 * 请求级容器.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ReqContainer extends ContainerContract
{
    /**
     * 是否实例化了.
     * @return bool
     */
    public function isInstanced() : bool;

    /**
     * 请求级容器应该要拿到应用实例.
     * @return App
     */
    public function getApp() : App;

    /**
     * 获取实例, 并且进行初始化.
     *
     * @param ContainerContract $procContainer
     * @return static
     */
    public function newInstance(ContainerContract $procContainer) : ReqContainer;

    /**
     * 请求级容器进行回收.
     */
    public function finish() : void;
}