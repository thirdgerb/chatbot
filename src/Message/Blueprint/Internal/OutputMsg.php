<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint\Internal;

use Commune\Message\Blueprint\ConvoMsg;
use Commune\Message\Blueprint\Message;
use Commune\Message\Blueprint\Reaction\ReactionMsg;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Babel\BabelSerializable;

/**
 * Ghost 到 Shell 的输出消息
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Message|ConvoMsg|ReactionMsg $message
 * @property-read Scope $scope
 */
interface OutputMsg extends BabelSerializable, ArrayAndJsonAble
{
}