<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Fallback;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Runtime\Process;
use Commune\Ghost\Prototype\Operators\AbsOperator;
use Commune\Ghost\Prototype\Operators\Retrace\WakeRetrace;
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
        $current = $this->process->challenge($thread, true);

        // 当前的 Context 进入回收, 只有 gc > 0  时有可能被唤醒
        if ($this->gcTurn > 0) {
            $this->process->addGc($current, $this->gcTurn);
        }

        // wake 当前的对话
        $node = $this->process->aliveThread()->currentNode();
        $stageDef = $node->findStageDef($conversation);
        $context = $node->findContext($conversation);
        $stage = new IActivateStage($conversation, $stageDef, $context);

        // wake
        return $stageDef->onWake($stage);
    }


}