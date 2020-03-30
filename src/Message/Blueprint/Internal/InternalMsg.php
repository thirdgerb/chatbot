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

use Commune\Message\Blueprint\Message;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Babel\BabelSerializable;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read Message $message
 * @property-read ShellScope $scope
 */
interface InternalMsg extends BabelSerializable, ArrayAndJsonAble
{
}