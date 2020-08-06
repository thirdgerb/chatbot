<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Callables;


/**
 * 认证器. 可以得到 true, false, null 三种结果.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Verifier
{
    public function __invoke() : ? bool;
}