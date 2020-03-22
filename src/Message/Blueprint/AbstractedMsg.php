<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint;


/**
 * 对消息的高级抽象
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface AbstractedMsg extends Message
{
    /**
     * 原始消息
     * @return ConvoMsg
     */
    public function getOrigin() : ConvoMsg;
}