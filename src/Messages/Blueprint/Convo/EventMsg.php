<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Messages\Blueprint\Convo;

use Commune\Messages\Blueprint\ConvoMsg;

/**
 * 作为事件的消息.通常不一定要响应.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface EventMsg extends ConvoMsg
{
    /**
     * 事件的名称
     * @return string
     */
    public function getEventName() : string;

}