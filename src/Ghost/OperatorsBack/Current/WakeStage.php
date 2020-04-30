<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\OperatorsBack\Current;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Ghost\Stage\IOnActivateStage;


/**
 * 让当前的 Stage wake
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class WakeStage implements Operator
{
    public function invoke(Cloner $cloner): ? Operator
    {
        $node = $cloner->runtime->getCurrentProcess()->aliveThread()->currentNode();

        $stageDef = $node->findStageDef($cloner);
        $stage = new IOnActivateStage(
            $cloner,
            $stageDef,
            $node
        );

        return $stageDef->onWake($stage);
    }


}