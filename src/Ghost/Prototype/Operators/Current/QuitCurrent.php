<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Current;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Process;
use Commune\Ghost\Blueprint\Runtime\Thread;
use Commune\Ghost\Prototype\Operators\End\QuitSession;
use Commune\Ghost\Prototype\Stage\IRetraceStage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class QuitCurrent implements Operator
{
    public function invoke(Conversation $conversation): ? Operator
    {
        $process = $conversation->runtime->getCurrentProcess();
        $thread = $process->aliveThread();

        // 退出事件, 父 node 是可以进行拦截的.
        while($popped = $thread->popNode()) {
            $current = $thread->currentNode();
            $stageDef = $current->findStageDef($conversation);
            $retrace = new IRetraceStage(
                $conversation,
                $stageDef,
                $current,
                $popped
            );

            $operator = $stageDef->onQuit($retrace);
            if (isset($operator)) {
                return $operator;
            }
        }

        // blocking 也有发言权.
        while($blocking = $process->popBlocking()) {
            $operator = $this->retraceThread($process, $blocking, $conversation);
            if (isset($operator)) {
                return $operator;
            }

        }

        // 所有的 Sleeping 也有关于 quit 的发言权. 但不会遍历了.
        while($sleeping = $process->popSleeping()) {

            $operator = $this->retraceThread($process, $sleeping, $conversation);
            if (isset($operator)) {
                return $operator;
            }
        }

        // 然后就真的退出了.
        return new QuitSession();
    }

    protected function retraceThread(Process $process, Thread $retrace, Conversation $conversation) : ? Operator
    {
        $popped = $process->replaceAliveThread($retrace)->currentNode();
        $current = $process->aliveThread()->currentNode();
        $stageDef = $current->findStageDef($conversation);
        $retrace = new IRetraceStage(
            $conversation,
            $stageDef,
            $current,
            $popped
        );

        return $stageDef->onQuit($retrace);
    }

}