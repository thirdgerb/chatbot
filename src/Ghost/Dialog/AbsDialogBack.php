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
use Commune\Ghost\Dialog\IFinale;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Ghost\Dialog\IWithdraw;
use Commune\Ghost\Dialog\IOperates\IFulfill;
use Commune\Blueprint\Ghost\Tools\Navigator;
use Commune\Blueprint\Ghost\Dialog\Finale\Await;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsDialogBack extends AbsDialog
{
    /*------- 链式调用 -------*/

    public function watch(Ucl $watcher): Navigator
    {
        $watcher = $watcher->toInstance($this->cloner);
        $process = $this->getProcess();

        // 添加 watch
        $this->pushStack(function() use ($process, $watcher){
            $process->addWatcher($watcher);
        });

        return $this;
    }

    public function sleep(Ucl $subject, array $wakeStages = []): Navigator
    {
        $subject = $subject->toInstance($this->cloner);
        $process = $this->getProcess();

        // 添加 watch
        $this->pushStack(function() use ($subject, $process, $wakeStages){
            $process->addSleeping($subject, $wakeStages);
        });
        return $this;
    }

    public function block(Ucl $subject, int $priority = null): Navigator
    {
        $subject = $subject->toInstance($this->cloner);
        $process = $this->getProcess();
        $priority = $priority
            ?? $subject->findContextDef($this->cloner)->getPriority();

        $this->pushStack(function() use ($subject, $process, $priority) {
            $process->addBlocking($subject, $priority);
        });

        return $this;
    }

    public function clearPath(): Navigator
    {
        $process = $this->getProcess();
        $ucl = $this->ucl;

        $this->pushStack(function() use ($process, $ucl) {
            $process->resetPath($ucl->getContextId());
        });

        return $this;
    }

    /*------- redirect -------*/

    public function next(string ...$stageNames): Dialog
    {
        $next = array_shift($stageNames);
        $process = $this->getProcess();

        $nextPathNotExists = empty($next)
            && empty($stageNames)
            && $process->pathExists($this->ucl->getContextId());


        //  如果没有后路了.
        if ($nextPathNotExists) {
            return new IOperates\IFulfill($this->cloner, $this->ucl, $this);
        }

        // 有后路则走 staging
        if (!empty($stageNames)) {
            $id = $this->ucl->getContextId();
            $this->pushStack(function() use ($process, $id, $stageNames) {
                $process->insertPath($id, $stageNames);
            });
        }

        return new IActivate\IStaging(
            $this->cloner,
            $this->ucl->goStage($next),
            $this
        );
    }

    public function circle(string $stageName, string ...$stageNames): Dialog
    {
        array_unshift($stageNames, $stageName);
        array_push($stageNames, $this->ucl->toEncodedStr());
        return $this->next(...$stageNames);
    }

    public function redirectTo(Ucl $target): Dialog
    {
        $target = $target->toInstance($this->cloner);
        $this->redirectTargetShouldNotSame($target);

        return new IActivate\IRedirect(
            $this->cloner,
            $target,
            $this
        );
    }


    /*------- self redirect -------*/

    protected function redirectTargetShouldNotSame(Ucl $target) : void
    {
        if ($target->isSameContext($this->ucl)) {
            throw new InvalidArgumentException(
                'should not block to same context, from '
                . $this->ucl->toEncodedStr()
                . ' to '
                . $target->toEncodedStr()
            );
        }
    }

    public function dependOn(Ucl $target, string $fieldName = null): Dialog
    {
        $target = $target->toInstance($this->cloner);
        $this->redirectTargetShouldNotSame($target);

        if (isset($fieldName)) {
            $context = $this->ucl->findContext($this->cloner);
            $depended = $target->findContext($this->cloner);

            $this->pushStack(function() use ($context, $fieldName, $depended){
                $context->offsetSet($fieldName, $depended);
            });
        }

        return new IActivate\IDepend(
            $this->cloner,
            $target,
            $this->ucl,
            $this
        );
    }

    public function blockTo(Ucl $target): Dialog
    {
        return $this->block($this->ucl)->redirectTo($target);
    }

    public function sleepTo(Ucl $target, array $wakenStages = []): Dialog
    {
        return $this->sleep($this->ucl, $wakenStages)->redirectTo($target);
    }


    public function reset(Ucl $root = null): Dialog
    {
        $root = $root ?? Ucl::make($this->cloner->scene->contextName);
        return new IActivate\IReset(
            $this->cloner,
            $root,
            $this
        );
    }

    /*------- withdraw -------*/


    public function fulfill(
        Ucl $target = null,
        int $gcTurns = 0,
        array $restoreStages = []
    ): Dialog
    {
        $ucl = $target ?? $this->ucl;

        return new IFulfill(
            $this->cloner,
            $ucl,
            $gcTurns,
            $restoreStages,
            $this
        );
    }


    public function cancel(
        Ucl $target = null
    ): Dialog
    {
        $ucl = $target ?? $this->ucl;
        return new IWithdraw\ICancel(
            $this->cloner,
            $ucl,
            $this
        );
    }

    public function confuse(): Dialog
    {
        return new IWithdraw\IConfuse(
            $this->cloner,
            $this->ucl,
            $this
        );
    }

    public function reject(
        Ucl $target = null
    ): Dialog
    {
        $ucl = $target ?? $this->ucl;
        return new IWithdraw\IReject(
            $this->cloner,
            $ucl,
            $this
        );
    }

    public function fail(
        Ucl $target = null
    ): Dialog
    {
        $ucl = $target ?? $this->ucl;

        return new IWithdraw\IFail(
            $this->cloner,
            $ucl,
            $this
        );
    }

    public function quit(): Dialog
    {
        return new IWithdraw\IQuit(
            $this->cloner,
            $this->ucl,
            $this
        );
    }

    /*------- await -------*/

    public function reactivate(): Dialog
    {
        return new IActivate\IReactivate(
            $this->cloner,
            $this->ucl,
            $this
        );
    }

    /*------- await -------*/

    public function await(
        array $stageInterceptors = [],
        array $contextInterceptors = [],
        int $expire = null
    ): Await
    {
        //todo
        return new IFinale\IAwait(
            $this->cloner,
            $this->ucl,
            $stageInterceptors,
            $contextInterceptors,
            $expire
        );
    }

    /*------- finale -------*/

    public function rewind(bool $silent = false): Dialog
    {
        return new IFinale\IRewind(
            $this->cloner,
            $this->ucl,
            $this,
            $silent
        );
    }

    public function dumb(): Dialog
    {
        return new IFinale\IDumb(
            $this->cloner,
            $this->ucl,
            $this
        );
    }

    public function backStep(int $step = 1): Dialog
    {
        return new IFinale\IBackStep(
            $this->cloner,
            $this->ucl,
            $this,
            $step
        );
    }

    /*------- inner  -------*/

}