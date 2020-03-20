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
use Commune\Messages\Blueprint\Tags\Verbal;

/**
 * 文本消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface TextMsg extends ConvoMsg, Verbal
{
}