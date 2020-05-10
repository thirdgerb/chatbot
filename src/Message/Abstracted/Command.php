<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Abstracted;

use Commune\Protocals\Abstracted;


/**
 * 将消息理解成一个命令语句.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string|null $cmdName       命令名
 * @property-read string|null $cmdStr        命令字符串
 */
interface Command extends Abstracted
{
}