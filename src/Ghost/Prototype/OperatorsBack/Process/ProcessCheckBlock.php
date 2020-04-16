<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\OperatorsBack\Process;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Prototype\OperatorsBack\AbsOperator;
use Commune\Ghost\Prototype\OperatorsBack\Staging\WakeStage;


/**
 * 检查 Process 的 blocking 优先级.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessCheckBlock extends AbsOperator
{
    public function invoke(Conversation $conversation): ? Operator
    {
        $process = $conversation->runtime->getCurrentProcess();

        if (!$process->hasBlocking()) {
            return new ProcessOnHear($process);
        }

        $current = $process->challengeAliveThread();

        // 挑战成功. wake current
        if (isset($current)) {
            $process->addSleepingThread($current);
            $node = $process->aliveThread()->currentNode();
            $stageDef = $node->findStageDef($conversation);
            return new WakeStage($stageDef, $node);
        }

        // 挑战失败, 继续走 hear
    }


}