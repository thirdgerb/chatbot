<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework\Session;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SessionEvent
{
    /**
     * 通常事件名就是类名. 但也要考虑各种特殊情况, 想要做面向对象的话.
     * @return string
     */
    public function getEventName() : string;
}