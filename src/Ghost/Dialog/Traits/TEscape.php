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
use Commune\Blueprint\Ghost\Dialogue\Finale\QuitSession;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\Task;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\DialogHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @method Process getProcess()
 */
trait TEscape
{

    protected function cancelCurrent() : ? Dialog
    {
        $process = $this->getProcess();
        $task = $process->popAliveTask();

        if (empty($task)) {
            return null;
        }

        // 将当前的 task 的后续任务列入取消计划.
        $dependedBy = $task->popCallbackUcl();
        $process->addGc($task);

        if (!empty($dependedBy)) {
            $process->addCanceling($dependedBy);
        }
        return null;
    }

    protected function iterateCanceling(string $type) : ? Dialog
    {
        $process = $this->getProcess();
        $uclStr = $process->popCanceling();

        // 没有 canceling 对象的话, 结束 canceling
        if (empty($uclStr)) {
            return null;
        }

        // 找到需要 cancel 的对象.
        $ucl = Ucl::decodeUcl($uclStr);
        $cancel = $this->buildEscaper($ucl, $type);

        // 调用 $stageDef->onEscape() 方法
        $next = DialogHelper::onEscaper($cancel);

        // 如果被中断了.
        if (isset($next)) {
            // 清空后续的 cancel 流程.
            $process->flushCanceling();
            return $next;
        }

        // 没有被中断的话, 要取消掉当前的 task 任务.
        $task = $process->popTask($ucl);

        // task 不存在的话, 就取消当前 cancel 流程.
        if (empty($task)) {
            return $this->iterateCanceling($type);
        }

        // task 存在, 尝试递归地退出.
        $canceling = $task->popCallbackUcl();
        if (!empty($canceling)) {
            $process->addCanceling($canceling);
        }

        return $this->iterateCanceling($type);
    }

    protected function doEscape(Process $process, Task $task, string $type) : ? Dialog
    {
        $alive = $process->challengeTask($task, true);
        $process->addGc($alive);

        $next = $this->buildEscaper($task->getUcl(), $type);
        return DialogHelper::onEscaper($next);
    }

    protected function escapeBlocking(string $type) : ? Dialog
    {
        $process = $this->getProcess();
        $blocking = $process->popBlocking();
        if (empty($blocking)) {
            return null;
        }

        return $this->doEscape($process, $blocking, $type);
    }

    protected function escapeSleeping(string $type) : ? Dialog
    {
        $process = $this->getProcess();
        $sleeping = $process->popSleeping();
        if (empty($sleeping)) {
            return null;
        }

        return $this->doEscape($process, $sleeping, $type);
    }

    protected function escapeWatching(string $type) : ? Dialog
    {
        $process = $this->getProcess();
        $watching = $process->popWatching();

        if (empty($watching)) {
            return null;
        }

        return $this->doEscape($process, $watching, $type);
    }

    protected function closeSession(Ucl $ucl) : Dialog
    {
        return $this->buildEscaper($ucl, QuitSession::class);
    }

}