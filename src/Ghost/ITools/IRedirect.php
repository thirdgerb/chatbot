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

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\Exceptions\BadNavigateCallException;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\IFinale;
use Commune\Ghost\Dialog\IResume;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Ghost\Dialog\IWithdraw;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Ghost\Dialog\IActivate;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Operate\Redirect;
use Commune\Ghost\Runtime\Operators\Dumb;
use Commune\Message\Host\SystemInt\TaskBusyInt;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Operate\Await;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRedirect implements Redirect
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

        // 如果重定向的目标是相同 context 内部.
        if ($this->ucl->isSameContext($target)) {
            return $this->next($target->stageName);
        }

        $contextId = $target->getContextId();
        $process = $this->getProcess();

        $currentStatus = $process->getContextStatus($contextId);
        $targetCurrentUcl = $process->getContextUcl($contextId);

        $bridge = null;
        if ($targetCurrentUcl->stageName !== $target->stageName) {
            $stage = $targetCurrentUcl->stageName;
            $bridge = function() use ($process, $contextId, $stage){
                $process->insertPath($contextId, $stage);
            };
        }

        switch ($currentStatus) {
            // await
            case Context::AWAIT:

                return $this->redirectToAwait($target);

            // preempt
            case Context::BLOCKING:

                return $this->redirectToBlocking($target);

                isset($bridge) and $this->dialog->pushStack($bridge);
                return new IActivate\IPreempt(
                    $this->cloner,
                    $targetCurrentUcl,
                    $this->dialog
                );

            // depending
            case Context::DEPENDING:

                return $this->redirectToDepending($target);

                // 递归地前进到依赖的起点. 有可能产生死循环.
                $dependedBy = $this->process
                    ->getDependedBy($target->getContextId());

                isset($bridge) and $this->dialog->pushStack($bridge);

                return $this->redirectTo($dependedBy);

            // callback
            case Context::CALLBACK:

                return $this->redirectToCallback($target);

                isset($bridge) and $this->dialog->pushStack($bridge);

                return new IRetain\ICallback(
                    $this->cloner,
                    $targetCurrentUcl,
                    $this->dialog
                );

            // wake
            case Context::SLEEPING:

                return $this->redirectToSleeping($target);

                isset($bridge) and $this->dialog->pushStack($bridge);

                return new IRetain\IWake(
                    $this->cloner,
                    $targetCurrentUcl,
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

                return new IActivate\IRestore(
                    $this->cloner,
                    $target, // 直接是 target
                    $this->dialog
                );

            // redirect
            default :

                return new IActivate\IRedirect(
                    $this->cloner,
                    $target, // 直接是 target
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
        if ($this instanceof Activate) {
            throw new BadNavigateCallException(
                $this->ucl->toEncodedStr(),
                'reactivate should not called by Activate Dialog'
            );
        }

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

        // 没有下一步
        $nextPathNotExists = empty($next)
            && empty($stageNames)
            && $process->pathExists($this->ucl->getContextId());


        //  如果没有后路了.
        if ($nextPathNotExists) {
            return $this->fulfill();
        }

        // 有后路则走 staging
        if (!empty($stageNames)) {
            $ucl = $this->ucl;
            $this->dialog->pushStack(function() use ($process, $ucl, $stageNames) {
                $process->insertPath($ucl, $stageNames);
            });
        }

        // 如果前进对象就是自己的话....
        if ($next === $this->dialog->ucl->stageName) {
            return $this->reactivate();
        }

        // 正常跳转.
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

    public function clearPath(): Redirect
    {
        $process = $this->getProcess();
        $ucl = $this->ucl;

        $this->dialog->pushStack(function() use ($process, $ucl) {
            $process->resetPath($ucl, []);
        });

        return $this;
    }


    public function fulfill(
        array $restoreStages = [],
        int $gcTurns = 0
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

    public function watch(Ucl $subject): Redirect
    {
        $watcher = $subject->toInstance($this->cloner);
        $process = $this->getProcess();

        // 添加 watch
        $this->dialog->pushStack(function() use ($process, $watcher){
            $process->addWatcher($watcher);
        });

        return $this;
    }

    public function sleep(Ucl $subject, array $wakeStages = []): Redirect
    {
        $sleep = $subject->toInstance($this->cloner);
        $process = $this->getProcess();

        $this->dialog->pushStack(function() use ($process, $sleep, $wakeStages) {
            $process->addSleeping($sleep, $wakeStages);
        });

        return $this;
    }

    public function block(Ucl $subject, int $priority = null): Redirect
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