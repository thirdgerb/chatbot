<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Message;


/**
 * 协议的抽象. 所有的协议都是 Interface.
 * 协议的所有参数理论上只能使用 @"property-read" 注解来做.
 * 当然也不一定了.
 *
 * 这个没有编译和运行时期的类型约束, 主要用于 IDE.
 *
 * 未来可以开发一个语言, 专门来实现这套协议的定义.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Protocal
{
}