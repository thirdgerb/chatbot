<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocols\HostMsg\Convo;

use Commune\Protocols\HostMsg\ConvoMsg;


/**
 * 事件类型的消息. 通常不用回复.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface EventMsg extends ConvoMsg
{
    // 常见客户端事件. 
    public function getEventName() : string;

    public function getPayload() : array;
}