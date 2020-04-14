<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Reply;

use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Prototype\Operators\AbsOperator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Confuse extends AbsOperator
{
    public function invoke(Conversation $conversation): ? Operator
    {
        // 如果父进程存在, 访问父进程的 Heard

        // 否则, 提交 Confuse message 并且结束循环.
    }


}