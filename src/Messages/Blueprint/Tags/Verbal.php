<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Messages\Blueprint\Tags;


/**
 * 文字类消息
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Verbal
{
    public function getTrimmedText() : string;
}