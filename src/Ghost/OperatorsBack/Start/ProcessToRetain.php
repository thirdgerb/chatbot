<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\OperatorsBack\Start;

use Commune\Framework\Blueprint\Intercom\RetainMsg;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Ghost\OperatorsBack\Current\RetainStage;
use Commune\Ghost\OperatorsBack\End\NoStateEnd;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessToRetain implements Operator
{
    /**
     * @var RetainMsg
     */
    protected $retainMsg;

    /**
     * ProcessToRetain constructor.
     * @param RetainMsg $retainMsg
     */
    public function __construct(RetainMsg $retainMsg)
    {
        $this->retainMsg = $retainMsg;
    }

    public function invoke(Cloner $cloner): ? Operator
    {
        $threadId = $this->retainMsg->getThreadId();
        $yielding = $cloner->runtime->findYielding($threadId);

        // 如果回调的目标 Thread 找不到了, 则放弃掉.
        // 会导致无状态的返回.
        if (!isset($yielding)) {
            // play dumb
            // todo 记录日志
            return new NoStateEnd();
        }

        $retainContext = $cloner->newContext(
            $this->retainMsg->getContextName(),
            $this->retainMsg->getEntities()
        );


        // 将 retainContext 插入当前的 Thread
        $yielding->pushNode($retainContext->toNewNode());

        // 将 yielding 还原到 block 中.
        $process = $cloner->runtime->getCurrentProcess();
        $process->blockThread($yielding);

        // 尝试 challenge
        $popped = $process->challengeAliveThread();

        // 挑战成功, 走 retain 流程.
        if (isset($popped)) {
            $process->addSleepingThread($popped);
            return new RetainStage();
        }

        // 挑战失败, 静观其变.
        return null;
    }



}