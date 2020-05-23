<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\ITools;

use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\IFinale;
use Commune\Ghost\Dialog\IRetain;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Ghost\Dialog\IWithdraw;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Ghost\Dialog\IActivate;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Tools\Navigator;
use Commune\Ghost\Runtime\Operators\Dumb;
use Commune\Message\Host\SystemInt\TaskBusyInt;
use Commune\Blueprint\Ghost\Runtime\Operator;
use Commune\Blueprint\Ghost\Dialog\Finale\Await;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class INavigator implements Navigator
{
    /**
     * @var AbsDialog
     */
    protected $dialog;

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var Ucl
     */
    protected $ucl;


    /*---- cached ----*/

    /**
     * @var Process
     */
    protected $process;

    /**
     * INavigator constructor.
     * @param AbsDialog $dialog
     */
    public function __construct(AbsDialog $dialog)
    {
        $this->dialog = $dialog;
        $this->cloner = $dialog->cloner;
        $this->ucl = $dialog->ucl;
    }


    protected function getProcess() : Process
    {
        return $this->process
            ?? $this->process = $this->cloner->runtime->getCurrentProcess();
    }

    public function redirectTo(Ucl $target): Operator
    {
        $target = $target->toInstance($this->cloner);

        if ($this->ucl->isSameContext($target)) {
            return $target->stageName === $this->ucl->stageName
                ? $this->reactivate()
                : $this->next($target->stageName);
        }

        $contextId = $target->getContextId();
        $status = $this->getProcess()->getContextStatus($contextId);

        switch ($status) {
            // await
            case Context::AWAIT:
                return $this->rewind();

            // preempt
            case Context::BLOCKING:
                return new IActivate\IPreempt(
                    $this->cloner,
                    $target,
                    $this->dialog
                );

            // depending
            case Context::DEPENDING:
                // 递归地前进到依赖的起点. 有可能产生死循环.
                $dependedBy = $this->process->getDependedBy($target->getContextId());
                return $this->redirectTo($dependedBy);

            // callback
            case Context::CALLBACK:
                return new IRetain\ICallback(
                    $this->cloner,
                    $target,
                    $this->dialog
                );

            // wake
            case Context::SLEEPING:
                return new IRetain\IWake(
                    $this->cloner,
                    $target,
                    $this->dialog
                );

            // task is busy
            case Context::YIELDING:

                $this->dialog
                    ->send()
                    ->message(new TaskBusyInt($target->toEncodedStr()));

                return $this->rewind();

            // restore
            case Context::DYING:

                return new IRetain\IRestore(
                    $this->cloner,
                    $target,
                    $this->dialog
                );

            // redirect
            default :

                return new IActivate\IRedirect(
                    $this->cloner,
                    $target,
                    $this->dialog
                );
        }
    }

    /*----- finale -----*/

    public function await(
        array $allowContexts = [],
        array $stageRoutes = [],
        int $expire = null
    ): Await
    {
        return new IFinale\IAwait(
            $this->cloner,
            $this->ucl,
            $stageRoutes,
            $allowContexts,
            $this->dialog,
            $expire
        );
    }

    public function rewind(bool $silent = false): Operator
    {
        return new IFinale\IRewind(
            $this->cloner,
            $this->ucl,
            $this->dialog,
            $silent
        );
    }

    public function reactivate(): Operator
    {
        return new IActivate\IReactivate(
            $this->cloner,
            $this->ucl,
            $this->dialog
        );
    }

    public function dumb(): Operator
    {
        return new Dumb($this->cloner);
    }

    public function backStep(int $step = 1): Operator
    {
        return new IFinale\IBackStep(
            $this->cloner,
            $this->ucl,
            $this->dialog,
            $step
        );
    }


    /*----- self -----*/

    public function next(string ...$stageNames): Operator
    {
        $next = array_shift($stageNames);
        $process = $this->getProcess();

        $nextPathNotExists = empty($next)
            && empty($stageNames)
            && $process->pathExists($this->ucl->getContextId());


        //  如果没有后路了.
        if ($nextPathNotExists) {
            return new IOperates\IFulfill(
                $this->cloner,
                $this->ucl,
                0,
                [],
                $this
            );
        }

        // 有后路则走 staging
        if (!empty($stageNames)) {
            $ucl = $this->ucl;
            $this->dialog->pushStack(function() use ($process, $ucl, $stageNames) {
                $process->insertPath($ucl, $stageNames);
            });
        }

        return new IActivate\IStaging(
            $this->cloner,
            $this->ucl->goStage($next),
            $this->dialog
        );
    }

    public function circle(string $stageName, string ...$stageNames): Operator
    {
        array_unshift($stageNames, $stageName);
        array_push($stageNames, $this->ucl->toEncodedStr());
        return $this->next(...$stageNames);
    }

    public function clearPath(): Navigator
    {
        $process = $this->getProcess();
        $ucl = $this->ucl;

        $this->dialog->pushStack(function() use ($process, $ucl) {
            $process->resetPath($ucl, []);
        });

        return $this;
    }


    public function fulfill(
        Ucl $target = null,
        int $gcTurns = 0,
        array $restoreStages = []
    ): Operator
    {
        $ucl = $target ?? $this->ucl;

        return new IOperates\IFulfill(
            $this->cloner,
            $ucl,
            $gcTurns,
            $restoreStages,
            $this
        );
    }

    /*----- redirect -----*/

    public function reset(Ucl $root = null): Operator
    {
        $root = $root ?? Ucl::make($this->cloner->scene->contextName);
        return new IActivate\IReset(
            $this->cloner,
            $root,
            $this
        );
    }

    public function dependOn(Ucl $dependUcl, string $fieldName = null): Operator
    {

        return new IActivate\IDepend(
            $this->cloner,
            $dependUcl,
            $this->ucl,
            $this->dialog
        );
    }

    public function blockTo(Ucl $target): Operator
    {
        return $this->block($this->ucl)->redirectTo($target);
    }

    public function sleepTo(Ucl $target, array $wakenStages = []): Operator
    {
        return $this->sleep($this->ucl, $wakenStages)->redirectTo($target);
    }

    /*----- quit -----*/

    public function quit(): Operator
    {
        return new IWithdraw\IQuit(
            $this->cloner,
            $this->ucl,
            $this->dialog
        );
    }


    public function confuse(): Operator
    {
        return new IWithdraw\IConfuse(
            $this->cloner,
            $this->ucl,
            $this->dialog
        );
    }

    /*----- withdraw -----*/

    public function cancel(
        Ucl $target = null
    ): Operator
    {
        $ucl = $target ?? $this->ucl;
        return new IWithdraw\ICancel(
            $this->cloner,
            $ucl,
            $this->dialog
        );
    }

    public function reject(
        Ucl $target = null
    ): Operator
    {
        $ucl = $target ?? $this->ucl;
        return new IWithdraw\IReject(
            $this->cloner,
            $ucl,
            $this->dialog
        );
    }

    public function fail(
        Ucl $target = null
    ): Operator
    {
        $ucl = $target ?? $this->ucl;
        return new IWithdraw\IFail(
            $this->cloner,
            $ucl,
            $this->dialog
        );
    }

    /*----- wait some -----*/

    public function watch(Ucl $subject): Navigator
    {
        $watcher = $subject->toInstance($this->cloner);
        $process = $this->getProcess();

        // 添加 watch
        $this->dialog->pushStack(function() use ($process, $watcher){
            $process->addWatcher($watcher);
        });

        return $this;
    }

    public function sleep(Ucl $subject, array $wakeStages = []): Navigator
    {
        $sleep = $subject->toInstance($this->cloner);
        $process = $this->getProcess();

        $this->dialog->pushStack(function() use ($process, $sleep, $wakeStages) {
            $process->addSleeping($sleep, $wakeStages);
        });

        return $this;
    }

    public function block(Ucl $subject, int $priority = null): Navigator
    {
        $block = $subject->toInstance($this->cloner);
        $process = $this->getProcess();
        $priority = $priority
            ?? $block->findContextDef($this->cloner)->getPriority();

        $this->dialog->pushStack(function() use ($process, $block, $priority) {
            $process->addBlocking($block, $priority);
        });

        return $this;
    }


}