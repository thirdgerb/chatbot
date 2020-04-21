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
 * 将输入消息理解成为一种选择
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string  $choice           选项 (index)
 * @property-read string  $answer           答案
 */
interface Choice extends Abstracted
{
}