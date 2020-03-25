<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\OnHear;

use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Operators\AbsOperator;


/**
 * Confuse
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Confuse extends AbsOperator
{
    public function invoke(): ? Operator
    {
        // 如果父进程存在, 构成回调 heard

        // 不存在的话, 则变成 rewind
    }


}