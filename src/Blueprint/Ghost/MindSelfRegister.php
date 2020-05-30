<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

/**
 * 可以注册到 Mindset 中的类. 通常通过 psr-4 来读取.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface MindSelfRegister
{
    const REGISTER_METHOD = 'selfRegisterToMind';

    public static function selfRegisterToMind(Mindset $mindset) : void;
}