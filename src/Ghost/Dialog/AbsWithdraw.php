<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog;

use Commune\Blueprint\Ghost\Dialog\Withdraw;
use Commune\Blueprint\Ghost\Runtime\Operator;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\Traits\TFallbackFlow;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsWithdraw extends AbsDialog implements Withdraw
{
    use TFallbackFlow;

    protected function withdrawCurrent(): Operator
    {
        $process = $this->getProcess();
        $process->unsetWaiting($this->_ucl);
        $process->addCanceling([$this->_ucl]);

        return $this->withdrawCanceling($process)
            ?? $this->fallbackFlow($process)
            ?? $this->quitBatch($process, $process->eachWatchers())
            ?? $this->quitSession();
    }

    protected function withdrawCanceling(Process $process) : ? Operator
    {
        $poppedDepends = [];

        while ($canceling = $process->popCanceling()) {
            $cancelingId = $canceling->getContextId();
            $poppedDepends = $this->pushDependingToCancel(
                $process,
                $cancelingId,
                $poppedDepends
            );

            $next = $this->doWithdraw($canceling);
            if (isset($next)) {
                // 有中断的话, 需要还原现场.
                $this->restoreCanceling($process, $poppedDepends);
                return $next;
            }
        }

        return null;
    }

    protected function doWithdraw(Ucl $ucl) : ? Operator
    {
        $intercept = new static($this->cloner, $ucl, $this);
        return $intercept->ucl
            ->findStageDef($this->cloner)
            ->onWithdraw($intercept);
    }

    protected function pushDependingToCancel(
        Process $process,
        string $cancelingId,
        array $poppedDepending
    ) : array
    {
        $allDepending = $process->dumpDepending($cancelingId);

        // 依赖的语境压入取消栈
        if (!empty($allDepending)) {

            // 准备好依赖关系的现场.
            $poppedDepending = array_reduce(
                $allDepending,
                function($poppedDepends, Ucl $ucl) use ($cancelingId){
                    $uclStr = $ucl->toEncodedStr();
                    $poppedDepends[$uclStr] = $cancelingId;
                },
                $poppedDepending
            );

            $process->addCanceling($allDepending);
        }

        return $poppedDepending;
    }


    protected function restoreCanceling(Process $process, array $poppedDepends) : void
    {
        $uncanceled = $process->dumpCanceling();
        foreach ($uncanceled as $un) {
            $ucl = $un->toEncodedStr();
            $process->addDepending(
                $un,
                $poppedDepends[$ucl]
            );
        }
    }

}