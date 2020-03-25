<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\OnBreak;

use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Operators\AbsOperator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Rewind extends AbsOperator
{
    public function invoke(): ? Operator
    {
        // 直接执行了, 就立刻 wait
        return null;
    }

}