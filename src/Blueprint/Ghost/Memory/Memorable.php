<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Memory;


/**
 * 可以被记忆的对象.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Memorable
{
    public function toStub() : Stub;
}