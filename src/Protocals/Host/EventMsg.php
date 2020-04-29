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
 * 事件类型的协议.
 * 通常是客户端和 Ghost 通讯, 而不广播.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface EventMsg extends HostMsg
{
    public function getEventName() : string;
}