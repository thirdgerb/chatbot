<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Process;

use Commune\Framework\Blueprint\Intercom\RetainMsg;
use Commune\Framework\Blueprint\Intercom\YieldMsg;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Prototype\Operators\AbsOperator;
use Commune\Ghost\Prototype\Operators\Hear\StageOnHear;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessStart extends AbsOperator
{
    public function invoke(Conversation $conversation): ? Operator
    {
        // 检查当前 message 的类型
        $message = $conversation->ghostInput->getMessage();

        // 检查是否是 yield
        if ($message instanceof YieldMsg) {
            return new ProcessFromYield($message);
        }

        if ($message instanceof RetainMsg) {
            return new ProcessToRetain($message);
        }

        $stageName = $conversation->runtime->getProcess()->aliveStageFullname();

        $stageDef = $conversation->mind->stageReg()->getDef($stageName);

        return new StageOnHear($stageDef);
    }

}