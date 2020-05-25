<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\OExiting;

use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\IOperate\OFinale\OCloseSession;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OQuit extends AbsExiting
{

    protected function toNext(): Operator
    {
        $target = $this->dialog->ucl;
        $process = $this->dialog->process;

        $this->addCanceling([$target]);

        return $this->recursiveWithdraw($process)
            ?? $this->quitBatch($process, $process->eachCallbacks())
            ?? $this->quitBatch($process, $process->eachBlocking())
            ?? $this->quitBatch($process, $process->eachSleeping())
            ?? $this->quitSession();
    }

    protected function doWithdraw(
        Process $process,
        Ucl $canceling
    ): ? Operator
    {
        $task = $process->getTask($canceling);

        $watch = $task->watchQuit();
        if (isset($watch)) {
            return $this->dialog->redirectTo($watch);
        }

        return null;
    }

    protected function quitSession()
    {
        return new OCloseSession($this->dialog);
    }

}