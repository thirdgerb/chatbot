<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Current;

use Commune\Blueprint\Ghost\Convo\Conversation;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Ghost\Operators\Fallback\CheckBlockBeforeWake;
use Commune\Ghost\Stage\IOnRetraceStage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RejectCurrent implements Operator
{
    public function invoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();
        $thread = $process->aliveThread();

        while($popped = $thread->popNode()) {

            $current = $thread->currentNode();
            $stageDef = $current->findStageDef($cloner);
            $retrace = new IOnRetraceStage(
                $cloner,
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