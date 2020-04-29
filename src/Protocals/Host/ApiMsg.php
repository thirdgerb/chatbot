<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Host;

use Commune\Protocals\HostMsg;

/**
 * Api 调用的消息. 通常是无状态的请求.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ApiMsg extends HostMsg
{
}