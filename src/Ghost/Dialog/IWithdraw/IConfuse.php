<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IWithdraw;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Blueprint\Ghost\Dialog\Withdraw\Confuse;
use Commune\Ghost\Dialog\DialogHelper;
use Commune\Ghost\Dialog\Traits\TIntentMatcher;
use Commune\Protocals\HostMsg\Convo\EventMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IConfuse extends AbsDialogue implements Confuse
{
    use TIntentMatcher;

    protected function runInterception(): ? Dialog
    {
        return null;
    }

    protected function runTillNext(): Dialog
    {
        $process = $this->getProcess();
        return $this->ifEventMsg()
            ?? $this->tryToWakeSleeping($process)
            ?? $this->tryToRestoreDying($process)
            ?? $this->passConfuseToWatcher($process)
            ?? $this->reallyConfuse($process);
    }

    protected function selfActivate(): void
    {
    }

    protected function ifEventMsg() : ? Dialog
    {
        $message = $this->cloner->input->getMessage();
        if ($message instanceof EventMsg) {
            return $this->nav()->dumb();
        }
        return null;
    }

    protected function tryToWakeSleeping(Process $process) : ? Dialog
    {
        $sleeping = $process->sleeping;
        if (empty($sleeping)) {
            return null;
        }

        foreach ($sleeping as $uclStr => $stages) {
            // empty
            if (empty($stages)) {
                continue;
            }

            $sleepingUcl = $process->decodeUcl($uclStr);
            $matched = $this->stageRoutesMatch($sleepingUcl, $stages);
            if (isset($matched)) {
                return DialogHelper::newDialog(
                    $this,
                    $matched,
                    Dialog\Retain\Wake::class
                );
            }
        }
        return null;
    }

    protected function tryToRestoreDying(Process $process) : ? Dialog
    {
        $dying = $process->dying;

        if (empty($dying)) {
            return null;
        }

        foreach ($dying as $uclStr => list($turns, $stages)) {
            if (empty($stages)) {
                continue;
            }

            $dyingUcl = $process->decodeUcl($uclStr);
            $matched = $this->stageRoutesMatch($dyingUcl, $stages);
            if (isset($matched)) {
                return DialogHelper::newDialog(
                    $this,
                    $matched,
                    Dialog\Retain\Restore::class
                );
            }
        }

        return null;
    }

    protected function passConfuseToWatcher(Process $process) : ? Dialog
    {
        $watching = $process->watching;

        if (empty($watching)) {
            return null;
        }

        foreach ($watching as $uclStr => $stages) {
            if (empty($stages)) {
                continue;
            }
            $ucl = $process->decodeUcl($uclStr);
            /**
             * @var Confuse $confuse
             */
            $confuse = DialogHelper::newDialog(
                $this,
                $ucl,
                Confuse::class
            );

            $next = DialogHelper::withdraw($confuse);
            if (isset($next)) {
                return $next;
            }
        }
        return null;
    }

    protected function reallyConfuse() : Dialog
    {
        //todo
    }
}