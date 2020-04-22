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
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $query             问题
 * @property-read string[] $suggestions     建议
 */
interface QuestionMsg extends ConvoMsg
{
}