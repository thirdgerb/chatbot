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

use Commune\Ghost\Dialog\Operate;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\IFinale;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Ghost\Dialog\IActivate;
use Commune\Ghost\Dialog\IWithdraw;
use Commune\Blueprint\Ghost\Tools\Navigator;
use Commune\Blueprint\Ghost\Dialog\Finale\Await;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsDialog extends AbsBaseDialog
{
    /*------- 链式调用 -------*/

    public function watch(Ucl $watcher): Navigator
    {
        $watcher = $watcher->toInstance($this->cloner);
        $process = $this->getProcess();

        // 添加 watch
        $this->nextStack[] = function() use ($process, $watcher){
            $process->addWatcher($watcher);
        };
        return $this;
    }

    public function sleep(Ucl $subject, array $wakeStages = []): Navigator
    {
        $subject = $subject->toInstance($this->cloner);
        $process = $this->getProcess();

        // 添加 watch
        $this->nextStack[] = function() use ($subject, $process, $wakeStages){
            $process->addSleeping($subject, $wakeStages);
        };
        return $this;
    }

    public function block(Ucl $subject, int $priority = null): Navigator
    {
        $subject = $subject->toInstance($this->cloner);
        $process = $this->getProcess();
        $priority = $priority
            ?? $subject->findContextDef($this->cloner)->getPriority();

        $this->nextStack[] = function() use ($subject, $process, $priority) {
            $process->addBlocking($subject, $priority);
        };

        return $this;
    }

    public function resetPath(): Navigator
    {
        $process = $this->getProcess();
        $ucl = $this->ucl;
        $this->nextStack[] = function() use ($process, $ucl) {
            $process->resetPath($ucl->getContextId());
        };

        return $this;
    }

    /*------- redirect -------*/

    public function next(string ...$stageNames): Dialog
    {
        $process = $this->getProcess();
        if (!empty($stageNames)) {
            $id = $this->ucl->getContextId();
            $this->nextStack[] = function() use ($process, $id, $stageNames) {
                $process->insertPath($id, $stageNames);
            };
        }

        return new Operate\GoNext(
            $this->cloner,
            $this->ucl,
            $this->popNextStack()
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
            $this->popNextStack()
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

        return new IActivate\IDepend(
            $this->cloner,
            $target,
            $this->ucl,
            $fieldName,
            $this->popNextStack()
        );
    }

    public function blockTo(Ucl $target): Dialog
    {
        $target = $target->toInstance($this->cloner);
        $this->redirectTargetShouldNotSame($target);
        return $this->block($this->ucl)->redirectTo($target);
    }

    public function sleepTo(Ucl $target, array $wakenStages = []): Dialog
    {
        $target = $target->toInstance($this->cloner);
        $this->redirectTargetShouldNotSame($target);
        return $this->sleep($this->ucl, $wakenStages)->redirectTo($target);
    }


    public function home(Ucl $root = null): Dialog
    {
        if (isset($root)) {
            $root = $root->toInstance($this->cloner);
        }

        $root = $root ?? $this->getProcess()->getRoot()->toInstance($this->cloner);
        return new IActivate\IHome(
            $this->cloner,
            $root,
            $this->popNextStack()
        );
    }

    /*------- withdraw -------*/

    public function cancel(
        Ucl $target = null
    ): Dialog
    {
        $ucl = $target ?? $this->ucl;
        $ucl = $ucl->toInstance($this->cloner);

        return new IWithdraw\ICancel(
            $this->cloner,
            $ucl,
            $this->popNextStack()
        );
    }

    public function confuse(): Dialog
    {
        return new IWithdraw\IConfuse(
            $this->cloner,
            $this->ucl,
            $this->popNextStack()
        );
    }

    public function fulfill(
        Ucl $target = null,
        int $gcTurns = 0,
        array $restoreStages = []
    ): Dialog
    {
        $ucl = $target ?? $this->ucl;
        $ucl = $ucl->toInstance($this->cloner);

        return new Operate\GoFulfill(
            $this->cloner,
            $ucl,
            $this->popNextStack()
        );
    }

    public function reject(
        Ucl $target = null
    ): Dialog
    {
        $ucl = $target ?? $this->ucl;
        $ucl = $ucl->toInstance($this->cloner);

        return new IWithdraw\IReject(
            $this->cloner,
            $ucl,
            $this->popNextStack()
        );
    }

    public function fail(
        Ucl $target = null
    ): Dialog
    {
        $ucl = $target ?? $this->ucl;
        $ucl = $ucl->toInstance($this->cloner);

        return new IWithdraw\IFail(
            $this->cloner,
            $ucl,
            $this->popNextStack()
        );
    }

    public function quit(): Dialog
    {
        return new IWithdraw\IQuit(
            $this->cloner,
            $this->ucl,
            $this->popNextStack()
        );
    }

    /*------- await -------*/

    public function reactivate(): Dialog
    {
        return new IActivate\IReactivate(
            $this->cloner,
            $this->ucl,
            $this->popNextStack()
        );
    }

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

    /*------- finale -------*/

    public function rewind(bool $silent = false): Dialog
    {
        return new IFinale\IRewind(
            $this->cloner,
            $this->ucl,
            $silent,
            $this->popStack()
        );
    }

    public function dumb(): Dialog
    {
        return new IFinale\IDumb($this->cloner, $this->ucl, $this->popStack());
    }

    public function backStep(int $step = 1): Dialog
    {
        return new IFinale\IBackStep($this->cloner, $this->ucl, $step, $this->popStack());
    }




    /*------- inner  -------*/

    protected function runAwait(bool $silent =false ) : void
    {
        $process = $this->getProcess();
        $waiter = $process->waiter;

        if (!isset($waiter) || $silent) {
            return;
        }

        // 如果是 waiter, 重新输出 question
        $question = $waiter->question;
        $input = $this->cloner->input;
        if (isset($question)) {
            $this->cloner->output($input->output($question));
        }

        // 尝试同步状态变更.
        $contextMsg = $this->cloner->runtime->toContextMsg();
        if (isset($contextMsg)) {
            $this->cloner->output($input->output($contextMsg));
        }
    }

}