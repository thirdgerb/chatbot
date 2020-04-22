<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Current;

use Commune\Blueprint\Ghost\Convo\Conversation;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Ghost\Stage\IOnActivateStage;


/**
 * 当前 Process 的 aliveThread 被 blockingThread 抢占成功时使用.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RetainStage implements Operator
{
    public function invoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();
        $node = $process->aliveThread()->currentNode();
        $stageDef = $node->findStageDef($cloner);

        $stage = new IOnActivateStage(
            $cloner,
            $stageDef,
            $node
        );

        return $stageDef->onRetain($stage);
    }


}