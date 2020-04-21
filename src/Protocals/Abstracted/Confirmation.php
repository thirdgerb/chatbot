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
 * 将用户的回答理解成一个确认
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read bool $positive        是否积极.
 */
interface Confirmation extends Abstracted
{
}