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
use Commune\Blueprint\Ghost\Tools\Navigator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\DialogHelper;
use Commune\Ghost\Dialog\IActivate;
use Commune\Ghost\Dialog\IFinale;
use Commune\Ghost\Dialog\IRedirect;
use Commune\Ghost\Dialog\IWithdraw;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 * @mixin AbsDialogue
 */
trait TRedirector
{
    /*------- 链式调用 -------*/

    public function watch(Ucl $watcher): Navigator
    {
        $process = $this->getProcess();
        $process->addWatcher($watcher);
        return $this;
    }

    public function resetPath(): Navigator
    {
        $process = $this->getProcess();
        $process->resetPath();
        return $this;
    }

    /*------- 重定向 -------*/

    public function goStage(string $stageName, string ...$pipes): Dialog
    {
        $ucl = $this->ucl->goStage($stageName);
        $paths = array_map(function($stage) use ($ucl){
            return $ucl->goStage($stage);
        }, $pipes);

        $staging = new IActivate\IStaging($this->cloner, $ucl, $paths);
        return $staging->withPrev($this);
    }

    public function redirectTo(Ucl $to, Ucl ...$pipes): Dialog
    {
        // 其实是 staging.
        if ($to->getContextId() === $this->ucl->getContextId()) {
            $next = new IActivate\IStaging($this->cloner, $to, $pipes);
        } else {
            $next = new IActivate\IRedirect($this->cloner, $to, $pipes);
        }

        return $next;
    }

    public function next(): Dialog
    {
        return new IRedirect\INext($this->cloner, $this->ucl);
    }

    /*------- await -------*/

    public function await(
        array $stageInterceptors = [],
        array $contextInterceptors = [],
        int $expire = null
    ): Await
    {
        return  new IFinale\IAwait(
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
        return new IActivate\IHome($this->cloner, $home);
    }

    public function dependOn(Ucl $depend, string $fieldName): Dialog
    {
        return new IActivate\IDepended($this, $depend, $fieldName);
    }

    public function blockTo(Ucl $to): Dialog
    {
        return new IRedirect\IBlockTo($this, $to);
    }

    public function sleepTo(Ucl $to = null, array $wakenStages = []): Dialog
    {
        return new IRedirect\ISleepTo($this, $wakenStages, $to);
    }

    public function yieldTo(
        string $shellName,
        string $guestId,
        Ucl $dependOn,
        Ucl $to = null
    ): Dialog
    {
        return new IRedirect\IYieldTo(
            $this->cloner,
            $this->ucl,
            $shellName,
            $guestId,
            $dependOn,
            $to
        );
    }

    public function restartContext(): Dialog
    {
        $ucl = $this->ucl->goStage('');
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
        return new IRedirect\IFulfill(
            $this->cloner,
            $this->ucl,
            $to,
            $restoreStages,
            $gcTurns
        );
    }

    public function cancelTo(Ucl $to = null): Dialog
    {
        return new IWithdraw\ICancel($this->cloner, $this->ucl, $to);
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
        $next = new IFinale\IRewind($this->cloner, $this->ucl, $silent);
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