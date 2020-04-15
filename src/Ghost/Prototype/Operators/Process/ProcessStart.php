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
use Commune\Ghost\Blueprint\Runtime\Process;
use Commune\Ghost\Prototype\Operators\AbsOperator;
use Commune\Ghost\Prototype\Operators\Staging\StageOnHear;

/**
 * 开始运行一个多轮对话的进程.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessStart extends AbsOperator
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

        $runtime = $conversation->runtime;

        // 当前 Context

        $process = $runtime->getProcess();
        $node = $process->aliveThread()->currentNode();

        $context = $node->findContext($conversation);
        $stageDef = $node->findStageDef($conversation);

        // 启动 Stage 的聆听环节.
        return new StageOnHear(
            $stageDef,
            $context
        );
    }

}