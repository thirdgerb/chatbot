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
use Commune\Ghost\Prototype\Stage\IOnActivateStage;


/**
 * 当前 Process 的 aliveThread 被 blockingThread 抢占成功时使用.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RetainStage implements Operator
{
    public function invoke(Conversation $conversation): ? Operator
    {
        $process = $conversation->runtime->getCurrentProcess();
        $node = $process->aliveThread()->currentNode();
        $stageDef = $node->findStageDef($conversation);

        $stage = new IOnActivateStage(
            $conversation,
            $stageDef,
            $node
        );

        return $stageDef->onRetain($stage);
    }


}