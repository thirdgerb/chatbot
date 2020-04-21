<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Intercom;


/**
 * Shell 上的输出消息, 由机器人发送.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellOutput extends ShellMessage
{
    public function getDeliverAt() : int;
}