<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Swoole;

use Swoole\Coroutine;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwooleUtils
{

    public static function isAtCoroutine() : bool
    {
        return class_exists(Coroutine::class)
            && Coroutine::getCid() > 0;
    }

}