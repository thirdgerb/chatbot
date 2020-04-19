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
 * 让当前的 Stage wake
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class WakeStage implements Operator
{
    public function invoke(Conversation $conversation): ? Operator
    {
        $node = $conversation->runtime->getCurrentProcess()->aliveThread()->currentNode();

        $stageDef = $node->findStageDef($conversation);
        $stage = new IOnActivateStage(
            $conversation,
            $stageDef,
            $node
        );

        return $stageDef->onWake($stage);
    }


}