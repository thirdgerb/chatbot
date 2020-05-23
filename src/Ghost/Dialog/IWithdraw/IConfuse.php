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
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Ghost\Dialog\IActivate;
use Commune\Protocals\HostMsg\Convo\EventMsg;
use Commune\Ghost\Dialog\Traits\TIntentMatcher;
use Commune\Blueprint\Ghost\Dialog\Withdraw\Confuse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IConfuse extends AbsDialog implements Confuse
{
    use TIntentMatcher;

    protected function runTillNext() : Operator
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

    /**
     * 事件类消息不需要专门响应.
     * @return Operator|null
     */
    protected function ifEventMsg() : ? Operator
    {
        $message = $this->cloner->input->getMessage();
        if ($message instanceof EventMsg) {
            return $this->redirect()->dumb();
        }
        return null;
    }

    protected function tryToWakeSleeping(Process $process) : ? Operator
    {
        $sleeping = $process->sleeping;
        if (empty($sleeping)) {
            return null;
        }

        foreach ($sleeping as $id => $stages) {
            // empty
            if (empty($stages)) {
                continue;
            }

            $sleepingUcl = $process->getContextUcl($id);
            $matched = $this->matchStageRoutes($sleepingUcl, $stages);
            if (isset($matched)) {
                return $this->redirect()->redirectTo($matched);
            }
        }
        return null;
    }

    protected function tryToRestoreDying(Process $process) : ? Operator
    {
        $dying = $process->dying;

        if (empty($dying)) {
            return null;
        }

        foreach ($dying as $id => list($turns, $stages)) {
            if (empty($stages)) {
                continue;
            }

            $dyingUcl = $process->getContextUcl($id);
            $matched = $this->matchStageRoutes($dyingUcl, $stages);
            if (isset($matched)) {
                return new IActivate\IRestore(
                    $this->_cloner,
                    $matched,
                    $this
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