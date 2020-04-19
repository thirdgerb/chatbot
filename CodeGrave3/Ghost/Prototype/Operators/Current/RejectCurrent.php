<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Current;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Prototype\Operators\Fallback\CheckBlockBeforeWake;
use Commune\Ghost\Prototype\Stage\IOnRetraceStage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RejectCurrent implements Operator
{
    public function invoke(Conversation $conversation): ? Operator
    {
        $process = $conversation->runtime->getCurrentProcess();
        $thread = $process->aliveThread();

        while($popped = $thread->popNode()) {

            $current = $thread->currentNode();
            $stageDef = $current->findStageDef($conversation);
            $retrace = new IOnRetraceStage(
                $conversation,
                $stageDef,
                $current,
                $popped
            );

            $operator = $stageDef->onReject($retrace);
            if (isset($operator)) {
                return $operator;
            }

        }

        // 要是成功地 cancel 了, 就开始 block 和 fallback
        return new CheckBlockBeforeWake($process);
    }


}