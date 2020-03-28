<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint\Convo;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface IntentMsg extends ConvoMsg
{
    public function getIntentName() : string;

    public function getEntities() : array;

}