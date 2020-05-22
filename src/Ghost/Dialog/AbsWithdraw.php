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

use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Withdraw;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Ghost\Dialog\IFinale\ICloseSession;
use Commune\Ghost\Dialog\IActivate\IPreempt;
use Commune\Ghost\Dialog\IRetain\IWake;
use Commune\Ghost\Dialog\IWithdraw\IQuit;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsWithdraw extends AbsDialog implements Withdraw
{
    protected function doWithdraw(Ucl $ucl, string $abstract = null) : ? Dialog
    {
        $abstract = $abstract ?? static::class;
        /**
         * @var Withdraw $intercept
         */
        $intercept = new $abstract($this->cloner, $ucl);
        $intercept = $intercept->withPrev($this);

        return $intercept->ucl
            ->findStageDef($this->cloner)
            ->onWithdraw($intercept);
    }

    protected function withdrawCanceling(Process $process) : ? Dialog
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

    protected function pushDependingToCancel(
        Process $process,
        string $cancelingId,
        array $poppedDepending
    ) : array
    {
        $allDepending = $process->popDepending($cancelingId);

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
    protected function fallbackFlow(Process $process) : Dialog
    {
        return $this->fallbackBlocking($process)
            ?? $this->fallbackSleeping($process)
            ?? $this->quitWatching($process)
            ?? $this->closeSession();
    }

    protected function closeSession() : Dialog
    {
        return new ICloseSession($this->cloner, $this->ucl, $this->dumpStack());
    }

    protected function fallbackBlocking(Process $process) : ? Dialog
    {
        // 检查 block
        $blocking = $process->popBlocking();

        if (!isset($blocking)) {
            return null;
        }

        return new IPreempt($this->cloner, $blocking, $this->dumpStack());
    }

    protected function fallbackSleeping(Process $process) : ? Dialog
    {
        // 检查 sleeping
        $sleeping = $process->popSleeping();
        if (isset($sleeping)) {
            return null;
        }

        return new IWake($this->cloner, $sleeping, $this->dumpStack());
    }


    protected function quitBlocking(Process $process) : ? Dialog
    {
        while($ucl = $process->popBlocking()) {

            $next = $this->doWithdraw($ucl, IQuit::class);
            if (isset($next)) {
                return $next;
            }
        }
        return null;

    }

    protected function quitSleeping(Process $process) : ? Dialog
    {
        while($ucl = $process->popSleeping()) {
            $next = $this->doWithdraw($ucl, IQuit::class);
            if (isset($next)) {
                return $next;
            }
        }

        return null;

    }


    protected function quitWatching(Process $process) : ? Dialog
    {
        while($watching = $process->popWatcher()) {
            $next = $this->doWithdraw($watching, IQuit::class);
            if (isset($next)) {
                return $next;
            }
        }
        return null;

    }


}