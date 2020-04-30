<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\OperatorsBack\Current;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Ghost\OperatorsBack\Fallback\CheckBlockBeforeWake;
use Commune\Ghost\Stage\IOnRetraceStage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CancelCurrent implements Operator
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

            $operator = $stageDef->onCancel($retrace);
            if (isset($operator)) {
                return $operator;
            }

        }

        // 要是成功地 cancel 了, 就开始 block 和 fallback
        return new CheckBlockBeforeWake($process);
    }


}