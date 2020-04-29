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
 * 指令类型的消息. 通常用于 Shell 与 Ghost 之间执行交互命令.
 * 是否广播完全看单个命令的设置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DirectiveMsg extends HostMsg
{
}