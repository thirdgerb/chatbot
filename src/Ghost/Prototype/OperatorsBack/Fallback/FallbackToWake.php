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
use Commune\Ghost\Prototype\OperatorsBack\Breakpoint\QuitSession;
use Commune\Ghost\Prototype\Stage\IActivateStage;

/**
 * fallback 的过程中, 尝试 wake 当前的 thread
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class FallbackToWake extends AbsOperator
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
     * FallbackToWake constructor.
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
        // 找到一个要醒的.
        $sleeping = $this->process->popSleeping();

        // 如果没有可以唤醒的, 则触发 quit 流程
        if (empty($sleeping)) {
            return new QuitSession();
        }

        $current = $this->process->challengeAliveThread($sleeping, true);

        // 当前的 Context 进入回收, 只有 gc > 0  时有可能被唤醒
        if ($this->gcTurn > 0) {
            $this->process->addGc($current, $this->gcTurn);
        }

        // wake 当前的对话
        $node = $this->process->aliveThread()->currentNode();
        $stageDef = $node->findStageDef($conversation);
        $context = $node->findContext($conversation);
        $stage = new IActivateStage($conversation, $stageDef, $context);

        // wake sleeping
        return $stageDef->onWake($stage);
    }


}