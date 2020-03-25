<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Internal;

use Commune\Message\Convo\ConvoMsg;
use Commune\Message\Message;
use Commune\Message\Reaction\ReactionMsg;
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