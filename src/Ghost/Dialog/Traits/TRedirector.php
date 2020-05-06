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
use Commune\Blueprint\Ghost\Operator\Await;
use Commune\Blueprint\Ghost\Routing\Redirector;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\DialogHelper;
use Commune\Ghost\Dialog\IActivate\IIntend;
use Commune\Ghost\Dialog\IActivate\IStaging;
use Commune\Ghost\Dialog\IFinale\IAwait;
use Commune\Ghost\Dialog\IFinale\IRewind;
use Commune\Ghost\Dialog\IStartProcess;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 * @mixin AbsDialogue
 */
trait TRedirector
{
    /*------- 链式调用 -------*/

    public function watch(Ucl $watcher): Redirector
    {
        $process = $this->getProcess();
        $process->addWatcher($watcher);
        return $this;
    }

    public function resetPath(): Redirector
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
            $next = new IIntend($this->cloner, $to, $pipes);
        }

        return $next->withPrev($this);
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
            $next = new IIntend($this->cloner, $to, []);
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
        $dialog = new IAwait(
            $this->cloner,
            $this->ucl,
            $stageInterceptors,
            $contextInterceptors,
            $expire
        );

        return $dialog->withPrev($this);
    }

    /*------- redirect -------*/

    public function home(
        Ucl $home = null,
        bool $restartProcess = false,
        bool $quiet = false
    ): Dialog
    {
        // TODO: Implement home() method.
    }

    public function dependOn(Ucl $depend, string $fieldName): Dialog
    {
        // TODO: Implement dependOn() method.
    }

    public function blockTo(Ucl $to): Dialog
    {
        // TODO: Implement blockTo() method.
    }

    public function sleepTo(Ucl $to = null, array $wakenStages = []): Dialog
    {
        // TODO: Implement sleepTo() method.
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

    public function restartProcess(): Dialog
    {
        return new IStartProcess($this->cloner);
    }

    public function restartContext(): Dialog
    {
        $ucl = $this->ucl->gotoStage('');
        return DialogHelper::newDialog($this, $ucl, Dialog\Activate\Staging::class);
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

    public function fulfillTo(Ucl $to = null, int $gcTurns = 0): Dialog
    {
        // TODO: Implement fulfillTo() method.
    }

    public function cancelTo(Ucl $to = null): Dialog
    {
        // TODO: Implement cancelTo() method.
    }

    public function reject(): Dialog
    {
        // TODO: Implement reject() method.
    }

    public function quit(): Dialog
    {
        // TODO: Implement quit() method.
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
        // TODO: Implement confuse() method.
    }


}