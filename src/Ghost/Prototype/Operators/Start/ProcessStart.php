<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Start;

use Commune\Framework\Blueprint\Intercom\RetainMsg;
use Commune\Framework\Blueprint\Intercom\YieldMsg;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Message\Blueprint\ContextMsg;
use Commune\Message\Blueprint\IntentMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessStart implements Operator
{
    public function invoke(Conversation $conversation): ? Operator
    {
        // 检查当前 message 的类型
        $message = $conversation->ghostInput->getMessage();

        // 检查是否是 yield, 否则走 yield 流程
        if ($message instanceof YieldMsg) {
            return new ProcessFromYield($message);
        }

        // 检查是否是 retain, 否则走 retain 的流程.
        if ($message instanceof RetainMsg) {
            return new ProcessToRetain($message);
        }

        // 检查是否是 ContextMsg, 如果是则走 intending 流程
        if ($message instanceof ContextMsg) {
            return new ProcessOnContextMsg($message);
        }

        // 检查是否有 blocking 要优先抢占.
        return new ProcessCheckBlock();
    }


}