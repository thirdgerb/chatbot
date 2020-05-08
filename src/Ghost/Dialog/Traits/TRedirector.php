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
use Commune\Blueprint\Ghost\Dialog\Finale\Await;
use Commune\Blueprint\Ghost\Routing\DialogManager;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\DialogHelper;
use Commune\Ghost\Dialog\IActivate\IRedirectTo;
use Commune\Ghost\Dialog\IActivate\IStaging;
use Commune\Ghost\Dialog\IFinale\IAwait;
use Commune\Ghost\Dialog\IFinale\IRewind;
use Commune\Ghost\Dialog\IRedirect\IBlockTo;
use Commune\Ghost\Dialog\IRedirect\IDependOn;
use Commune\Ghost\Dialog\IRedirect\IFulfill;
use Commune\Ghost\Dialog\IRedirect\IHome;
use Commune\Ghost\Dialog\IRedirect\ISleepTo;
use Commune\Ghost\Dialog\IWithdraw\ICancel;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 * @mixin AbsDialogue
 */
trait TRedirector
{
    /*------- 链式调用 -------*/

    public function watch(Ucl $watcher): DialogManager
    {
        $process = $this->getProcess();
        $process->addWatcher($watcher);
        return $this;
    }

    public function resetPath(): DialogManager
    {
        $process = $this->getProcess();
        $process->resetPath();
        return $this;
    }

    /*------- 重定向 -------*/

    public function goStage(string $stageName, string ...$pipes): Dialog
    {
        $ucl = $this->ucl->gotoStage($stageName);
        $paths = array_map(function($stage) use ($ucl){
            return $ucl->gotoStage($stage);
        }, $pipes);

        $staging = new IStaging($this->cloner, $ucl, $paths);
        return $staging->withPrev($this);
    }

    public function redirectTo(Ucl $to, Ucl ...$pipes): Dialog
    {
        // 其实是 staging.
        if ($to->getContextId() === $this->ucl->getContextId()) {
            $next = new IStaging($this->cloner, $to, $pipes);
        } else {
            $next = new IRedirectTo($this->cloner, $to, $pipes);
        }

        return $next;
    }

    public function next(): Dialog
    {
        $process = $this->getProcess();
        $nextStr = $process->popPath();

        // 没有下一步的话, 则等于 fulfill.
        if (empty($nextStr)) {
            return $this->fulfillTo();
        }

        $to = $process->decodeUcl($nextStr);
        if ($this->ucl->getContextId() === $to->getContextId()) {
            $next = new IStaging($this->cloner, $to, []);
        } else {
            $next = new IRedirectTo($this->cloner, $to, []);
        }

        return $next->withPrev($this);
    }

    /*------- await -------*/

    public function await(
        array $stageInterceptors = [],
        array $contextInterceptors = [],
        int $expire = null
    ): Await
    {
        return  new IAwait(
            $this->cloner,
            $this->ucl,
            $stageInterceptors,
            $contextInterceptors,
            $expire
        );
    }

    /*------- redirect -------*/

    public function home(Ucl $home = null): Dialog
    {
        return new IHome($this->cloner, $home);
    }

    public function dependOn(Ucl $depend, string $fieldName): Dialog
    {
        return new IDependOn($this, $depend, $fieldName);
    }

    public function blockTo(Ucl $to): Dialog
    {
        return new IBlockTo($this, $to);
    }

    public function sleepTo(Ucl $to = null, array $wakenStages = []): Dialog
    {
        return new ISleepTo($this, $wakenStages, $to);
    }

    public function yieldTo(
        string $shellName,
        string $guestId,
        Ucl $depend,
        Ucl $to = null
    ): Dialog
    {
        // TODO: Implement yieldTo() method.
    }

    public function restartContext(): Dialog
    {
        $ucl = $this->ucl->gotoStage('');
        return DialogHelper::newDialog(
            $this,
            $ucl,
            Dialog\Activate\Staging::class
        );
    }

    public function restartStage(): Dialog
    {
        return DialogHelper::newDialog(
            $this,
            $this->ucl,
            Dialog\Activate\Staging::class
        );
    }

    /*------- withdraw -------*/

    public function fulfillTo(Ucl $to = null, array $restoreStages = [], int $gcTurns = 1) : Dialog
    {
        return new IFulfill(
            $this->cloner,
            $this->ucl,
            $to,
            $restoreStages,
            $gcTurns
        );
    }

    public function cancelTo(Ucl $to = null): Dialog
    {
        return new ICancel($this->cloner, $this->ucl, $to);
    }

    public function reject(): Dialog
    {
        return DialogHelper::newDialog(
            $this,
            $this->ucl,
            Dialog\Withdraw\Reject::class
        );
    }

    public function quit(): Dialog
    {
        return DialogHelper::newDialog(
            $this,
            $this->ucl,
            Dialog\Withdraw\Quit::class
        );
    }


    /*------- Finale -------*/

    public function rewind(bool $silent = false): Dialog
    {
        $next = new IRewind($this->cloner, $this->ucl, $silent);
        return $next->withPrev($this);
    }

    public function dumb(): Dialog
    {
        return DialogHelper::newDialog($this, $this->ucl, Dialog\Finale\Dumb::class);
    }

    public function backStep(int $step = 1): Dialog
    {
        if ($step > 0) {
            $process = $this->getProcess();
            $process->backStep($step);
        }
        return $this->rewind(false);
    }

    public function confuse(): Dialog
    {
        return DialogHelper::newDialog(
            $this,
            $this->ucl,
            Dialog\Withdraw\Confuse::class
        );
    }


}