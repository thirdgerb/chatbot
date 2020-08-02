<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Platform;


/**
 * 将平台的输入输出打包, 传给 adapter
 * 用这种方式实现 adapter 在平台上的替换.
 * 一个平台只有一种确定的 packer, 但可以使用不同的 adapter.
 *
 * Platform 自身可以定义 Packer
 * 而 PlatformConfig 可以替换 Adapter
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Packer
{

    /**
     * 数据包是否有问题.
     * 如果有问题, 则直接告知客户端请求失败.
     * @return null|string
     */
    public function isInvalidInput() : ? string;

    /**
     * 通过数据包生成一个适配器.
     *
     * @param string $adapterName
     * @param string $appId
     * @return Adapter
     */
    public function adapt(string $adapterName, string $appId) : Adapter;

    /**
     * 用于表示请求本身失败, 而无关逻辑结果.
     * 通常是 400 bad request.
     * 将请求失败的消息告知客户端.
     *
     * @param string $error
     */
    public function fail(string $error) : void;

    /**
     * 避免垃圾回收有问题, 提供一个主动清除持有对象的机会.
     */
    public function destroy() : void;
}