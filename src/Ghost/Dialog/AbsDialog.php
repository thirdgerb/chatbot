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

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Finale\Await;
use Commune\Blueprint\Ghost\Tools\Navigator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\IActivate\IDepend;
use Commune\Ghost\Dialog\IActivate\IHome;
use Commune\Ghost\Dialog\IActivate\IReactivate;
use Commune\Ghost\Dialog\IActivate\IRedirect;
use Commune\Ghost\Dialog\IFinale;
use Commune\Ghost\Dialog\IWithdraw\ICancel;
use Commune\Ghost\Dialog\Temp\GoNext;


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

        return new GoNext($this->cloner, $this->ucl, $this->popNextStack());
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
        return new IRedirect($this->cloner, $target, $this->popNextStack());
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

        return new IDepend(
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
        return new IHome($this->cloner, $root, $this->popNextStack());
    }

    /*------- withdraw -------*/


    public function cancel(
        Ucl $target = null
    ): Dialog
    {
        $ucl = $target ?? $this->ucl;
        $ucl = $ucl->toInstance($this->cloner);

        return new ICancel($this->cloner, $ucl, $this->popNextStack());
    }















//
//    public function goStage(string $stageName, string ...$pipes): Dialog
//    {
//        $ucl = $this->ucl->goStage($stageName);
//        $paths = array_map(function($stage) use ($ucl){
//            return $ucl->goStage($stage);
//        }, $pipes);
//
//        $staging = new IActivate\IStaging($this->cloner, $ucl, $paths);
//        return $staging->withPrev($this);
//    }
//
//    public function redirectTo(Ucl $to, Ucl ...$pipes): Dialog
//    {
//        // 其实是 staging.
//        if ($to->getContextId() === $this->ucl->getContextId()) {
//            $next = new IActivate\IStaging($this->cloner, $to, $pipes);
//        } else {
//            $next = new IActivate\IRedirect($this->cloner, $to, $pipes);
//        }
//
//        return $next;
//    }


    public function reactivate(): Dialog
    {
        return new IReactivate($this->cloner, $this->ucl, $this->popNextStack());
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
        if ($step <= 0) {
            throw new InvalidArgumentException("back step should greater than 0, $step given");
        }

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


//
//    public function fulfillTo(Ucl $to = null, array $restoreStages = [], int $gcTurns = 1) : Dialog
//    {
//        return new IRedirect\IFulfill(
//            $this->cloner,
//            $this->ucl,
//            $to,
//            $restoreStages,
//            $gcTurns
//        );
//    }
//
//    public function cancelTo(Ucl $to = null): Dialog
//    {
//        return new IWithdraw\ICancel($this->cloner, $this->ucl, $to);
//    }
//
//    public function reject(): Dialog
//    {
//        return DialogHelper::newDialog(
//            $this,
//            $this->ucl,
//            Dialog\Withdraw\Reject::class
//        );
//    }
//
//    public function quit(): Dialog
//    {
//        return DialogHelper::newDialog(
//            $this,
//            $this->ucl,
//            Dialog\Withdraw\Quit::class
//        );
//    }


    /*------- Finale -------*/
//
//    public function rewind(bool $silent = false): Dialog
//    {
//        $next = new IFinale\IRewind($this->cloner, $this->ucl, $silent);
//        return $next->withPrev($this);
//    }
//
//    public function dumb(): Dialog
//    {
//        return DialogHelper::newDialog($this, $this->ucl, Dialog\Finale\Dumb::class);
//    }
//
//    public function backStep(int $step = 1): Dialog
//    {
//        if ($step > 0) {
//            $process = $this->getProcess();
//            $process->backStep($step);
//        }
//        return $this->rewind(false);
//    }
//
//    public function confuse(): Dialog
//    {
//        return DialogHelper::newDialog(
//            $this,
//            $this->ucl,
//            Dialog\Withdraw\Confuse::class
//        );
//    }


}