<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Protocol;


/**
 * 协议的抽象. 所有的协议都是 Interface.
 *
 * 协议的所有参数建议使用 @"property-read" 或 @"property" 注解来定义.
 * 也可以定义方法, 不过方法显然不能跨语言传输.
 *
 * 这个没有编译和运行时期的类型约束, 主要用于 IDE.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Protocol
{
    /**
     * 标记协议独立身份的 ID, 从而允许过滤协议使用的 handler
     * @return string
     */
    public function getProtocolId() : string;
}