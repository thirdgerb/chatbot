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
class FulfillCurrent implements Operator
{

    /**
     * @var int
     */
    protected $gcTurn;

    /**
     * FulfillContext constructor.
     * @param int $gcTurn
     */
    public function __construct(int $gcTurn = 0)
    {
        $this->gcTurn = $gcTurn;
    }


    public function invoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();
        $thread = $process->aliveThread();
        $popped = $thread->popNode();

        // 当 popped 就是根节点时, 允许在垃圾回收过程中, 仍然被唤醒.
        if ($this->gcTurn > 0 && isset($popped)) {
            $process->addGcThread($popped->toThread(), $this->gcTurn);
        }

        // 尝试回退. 调用 retrace
        while(isset($popped)) {

            $current = $thread->currentNode();
            $stageDef = $current->findStageDef($cloner);
            $retrace = new IOnRetraceStage(
                $cloner,
                $stageDef,
                $current,
                $popped
            );

            $operator = $stageDef->onFulfill($retrace);
            if (isset($operator)) {
                return $operator;
            }

            $popped = $thread->popNode();
        }

        // 如果回退没有拦截, 则走 CheckBlock
        return new CheckBlockBeforeWake($process);
    }


}