<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\OperatorsBack\Fallback;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Process;
use Commune\Ghost\Prototype\OperatorsBack\AbsOperator;
use Commune\Ghost\Prototype\OperatorsBack\Staging\WakeStage;
use Commune\Ghost\Prototype\Stage\IActivateStage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class FallbackToBlock extends AbsOperator
{
    /**
     * @var Process
     */
    protected $process;

    /**
     * @var int
     */
    protected $gcTurn;

    /**
     * FallbackToBlock constructor.
     * @param Process $process
     * @param int $gcTurn
     */
    public function __construct(Process $process, int $gcTurn)
    {
        $this->process = $process;
        $this->gcTurn = $gcTurn;
    }


    public function invoke(Conversation $conversation): ? Operator
    {
        // 如果没有需要 block 的.
        if (!$this->process->hasBlocking()) {
            return new FallbackToWake($this->process, $this->gcTurn);
        }

        $thread = $this->process->popBlocking();

        // 强制占领当前对话.
        $current = $this->process->challengeAliveThread($thread, true);

        // 当前的 Context 进入回收, 只有 gc > 0  时有可能被唤醒
        if ($this->gcTurn > 0) {
            $this->process->addGc($current, $this->gcTurn);
        }

        // wake 当前的对话
        $node = $this->process->aliveThread()->currentNode();
        $stageDef = $node->findStageDef($conversation);


        // wake. 暂时 block 的觉醒流程是 wake
        // 一个异步任务可以在 activate 环节 yield 自身
        // 然后在 retain 的时候, 通过 wake 方法来调用后续流程.
        // 目前考虑任务的 Context 在不同 cloneId 之间投递.
        return new WakeStage($stageDef, $node);
    }


}