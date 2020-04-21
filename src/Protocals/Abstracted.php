<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals;

use Commune\Support\Message\Message;
use Commune\Support\Message\Protocal;


/**
 * 对输入消息的高度抽象.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Abstracted extends Message, Protocal
{
}