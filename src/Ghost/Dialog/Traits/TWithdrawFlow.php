<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\Traits;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\Operator;


/**
 * 允许当前语境执行 withdraw 流程.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @mixin AbsDialog
 */
trait TWithdrawFlow
{
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


    protected function doWithdraw(Ucl $ucl, string $abstract = null) : ? Operator
    {
        $abstract = $abstract ?? static::class;
        /**
         * @var Dialog\Withdraw $intercept
         */
        $intercept = new $abstract($this->cloner, $ucl, $this);
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