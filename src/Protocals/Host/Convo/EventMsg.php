<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Host\Convo;

use Commune\Protocals\Host\ConvoMsg;


/**
 * 事件类型的协议. 对于会话而言, 有可能有影响, 有可能没有影响.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $eventName
 */
interface EventMsg extends ConvoMsg
{
}