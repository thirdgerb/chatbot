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
 * 参数校验类. 用于校验参数.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ParamValidator
{
    public function __invoke($value) : bool;
}