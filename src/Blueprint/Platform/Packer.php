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
 * 用这种方式实现 adapter 在平台上的复用.
 *
 * Platform 自身可以定义 Packer
 * 而 PlatformConfig 可以替换 Adapter
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Packer
{

    /**
     * @return null|string
     */
    public function isInvalid() : ? string;

    /**
     * @param string $adapterName
     * @return Adapter
     */
    public function adapt(string $adapterName) : Adapter;

    public function fail(string $error) : void;

    /**
     * 避免垃圾回收有问题, 提供一个主动清除持有对象的机会.
     */
    public function destroy() : void;
}