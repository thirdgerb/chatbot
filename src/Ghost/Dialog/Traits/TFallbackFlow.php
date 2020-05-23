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

use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Ghost\Dialog\IActivate;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Dialog\IResume;
use Commune\Ghost\Dialog\IWithdraw\IQuit;
use Commune\Ghost\Runtime\Operators\CloseSession;

/**
 * 允许当前语境执行 fallback 流程.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @mixin AbsDialog
 *
 */
trait TFallbackFlow
{

    protected function fallbackFlow(Process $process) : ? Operator
    {
        return $this->fallbackToCallbacks($process)
            ?? $this->fallbackToBlocking($process)
            ?? $this->fallbackToSleeping($process)
            ?? $this->fallbackToRoot($process);
    }

    protected function fallbackToRoot(Process $process) : Operator
    {
        $root = $process->getRoot();

        // 回到根节点.
        if (! $root->isSameContext($this->_ucl)) {
            return $this->redirect()->redirectTo($root);
        }

        // 当前就是根节点, 就直接退出.
        return $this->quitBatch($process, $process->eachYielding())
            ?? $this->quitBatch($process, $process->eachWatchers())
            ?? $this->quitSession();
    }

    protected function fallbackToCallbacks(Process $process) : ? Operator
    {
        $callback = $process->firstCallback();
        if (!isset($callback)) {
            return null;
        }

        return new IRetain\ICallback(
            $this->_cloner,
            $callback,
            $this
        );
    }

    protected function fallbackToBlocking(Process $process) : ? Operator
    {
        // 检查 block
        $blocking = $process->firstBlocking();

        if (!isset($blocking)) {
            return null;
        }

        return new IActivate\IPreempt(
            $this->cloner,
            $blocking,
            $this
        );
    }

    protected function fallbackToSleeping(Process $process) : ? Operator
    {
        $sleeping = $process->firstSleeping();

        if (!isset($sleeping)) {
            return null;
        }

        return new IRetain\IWake(
            $this->cloner,
            $sleeping,
            $this
        );
    }

    protected function quitBatch(Process $process, \Generator $generator) : ? Operator
    {
        foreach ($generator as $ucl) {
            $next = $this->tryToQuitUcl($process, $ucl);
            if (isset($next)) {
                return $next;
            }
        }
        return null;
    }

    protected function tryToQuitUcl(Process $process, Ucl $ucl) : ? Operator
    {
        $quit = new IQuit($this->_cloner, $ucl, $this);

        $next = $ucl->findStageDef($this->_cloner)->onWithdraw($quit);

        if (!isset($next) && $ucl->stageName !== '') {
            $initStage = $ucl->goStage('');
            $next = $initStage
                ->findStageDef($this->_cloner)
                ->onWithdraw(new IQuit($this->_cloner, $initStage, $this));
        }

        if (isset($next)) {
            return $next;
        }

        $process->unsetWaiting($ucl);
        $process->addCanceling([$ucl]);
        return $this->withdrawCanceling($process);
    }

    protected function quitSession() : Operator
    {
        return new CloseSession($this);
    }

}