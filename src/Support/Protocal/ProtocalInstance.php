<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Protocal;


/**
 * 协议实例. 表示是若干种 protocal 的实现.
 * 用于表示消息实现了哪些协议, 可以用于这些协议的指令.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ProtocalInstance
{

    /**
     * 检查是否是一个协议的实例.
     * @param string $protocalName
     * @return bool
     */
    public function isProtocal(string $protocalName) : bool;

    /**
     * 转化自身为某个协议对象.
     * 协议本身就是 interface 而已.
     * 添加这一步也是为了解耦更彻底.
     *
     * @param string $protocalName
     * @return static|null
     */
    public function toProtocal(string $protocalName) : ? Protocal;

    /**
     * 获取所有实现的协议名称.
     * 通常是类名的 dot 表示法
     * @return string[]
     */
    public function getProtocals() : array;

}