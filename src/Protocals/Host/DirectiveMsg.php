<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Host\Ghost;

use Commune\Protocals\HostMsg;


/**
 * 指令类型的消息. 通常用于各个模块之间采取一些响应.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DirectiveMsg extends HostMsg
{
}