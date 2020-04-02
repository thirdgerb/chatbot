<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Render;

use Commune\Message\Blueprint\IntentMsg;
use Commune\Message\Blueprint\Message;

/**
 * 将一个意图数据, 渲染成若干个常规 Message
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Template
{

    /**
     * @param IntentMsg $message
     * @return Message[]
     */
    public function render(IntentMsg $message) : array;

}