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
 * 事件类型的消息. 通常不用回复.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface EventMsg extends ConvoMsg
{
    public function getEventName() : string;

    public function getPayload() : array;
}