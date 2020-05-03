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
use Commune\Blueprint\Ghost\Dialogue\Finale\CloseSession;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\Task;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\DialogHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @mixin AbsDialogue
 */
trait TEscape
{

    protected function escapeDepended(string $type) : ? Dialog
    {
        $process = $this->getProcess();
        $task = $process->aliveTask();
        $dependedBy = $task->callbackUcl;

        if (empty($dependedBy)) {
            return null;
        }
        $depended = $process->popTask($dependedBy->getContextId());
        if (empty($depended)) {
            return null;
        }

        return $this->doEscape($process, $depended, $type);
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
        return $this->buildEscaper($ucl, CloseSession::class);
    }

}