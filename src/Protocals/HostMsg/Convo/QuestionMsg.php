<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\HostMsg\Convo;

use Commune\Protocals\HostMsg\ConvoMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface QuestionMsg extends ConvoMsg
{
    public function getQuery() : string;

    public function getSuggestions() : array;
}