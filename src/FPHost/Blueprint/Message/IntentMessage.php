<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\FPHost\Blueprint\Message;

use Commune\Chatbot\Blueprint\Message\Message;

/**
 * 当命中某个意图后, 对命中结果进行封装.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface IntentMessage extends \ArrayAccess, Message
{

    /**
     * 命中的意图名称
     * @return string
     */
    public function intentName() : string;

    /**
     *
     * @return array
     */
    public function toEntities() : array;

}