<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\OExiting;

use Commune\Blueprint\Ghost\Runtime\Task;
use Generator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\IOperate\AbsOperator;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Operate\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsExiting extends AbsOperator
{
    /**
     * @var Ucl[]
     */
    protected $canceling = [];

    protected function addCanceling(Ucl ...$canceling) : void
    {
        array_push($this->canceling, ...$canceling);
    }

    protected function popCanceling() : ? Ucl
    {
        return array_shift($this->canceling);
    }


    protected function doWithdraw(Process $process, Ucl $canceling) : ? Operator
    {
        $task = $process->getTask($canceling);
        $watcher = $this->getWithdrawWatcher($task);

        if (!isset($watcher)) {
            return null;
        }

        $current = $this->dialog->ucl;
        // 自己内部的 withdraw 不用拦截. 完全可以自己重定向去退出.
        if ($watcher->equals($current)) {
            return null;
        }

        return $this->dialog->redirectTo($watcher);
    }

    abstract protected function getWithdrawWatcher(Task $task) : ? Ucl;


    protected function recursiveWithdraw(Process $process) : ? Operator
    {
        while ($canceling = $this->popCanceling()) {

            $cancelingId = $canceling->getContextId();
            $allDepending = $process->dumpDepending($cancelingId);

            $this->addCanceling(...$allDepending);
            $next = $this->doWithdraw($process, $canceling);

            if (isset($next)) {
                return $next;
            }

            // 没有拦截的话, 就添加到 dying
            // 这样就能取消掉 depending 关系
            $process->addDying($canceling);
        }

        return null;
    }

    protected function restoreCanceling(Process $process, array $poppedDepends) : void
    {
        $uncanceled = $this->canceling;
        foreach ($uncanceled as $un) {
            $ucl = $un->encode();
            $process->addDepending(
                $un,
                $poppedDepends[$ucl]
            );
        }
    }

    protected function quitBatch(Process $process, Generator $each) : ? Operator
    {
        foreach ($each as $ucl) {

            $this->addCanceling($ucl);
            $next = $this->recursiveWithdraw($process);
            if (isset($next)) {
                return $next;
            }
        }

        return null;
    }
}