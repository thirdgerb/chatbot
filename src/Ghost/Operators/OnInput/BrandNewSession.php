<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\OnInput;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Ghost\Operators\FlowOperator;
use Commune\Ghost\Operators\Flows\InputFlow;
use Commune\Ghost\Operators\ReincarnationOperator;
use Commune\Ghost\Routes\Activate\IStaging;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BrandNewSession extends FlowOperator
{
    public function doInvoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();
        if (!$process->isBrandNew()) {
            return null;
        }

        $current = $process->currentTask();
        $stageDef = $current->findStageDef($cloner);

        $staging = new IStaging(
            $cloner,
            $current
        );

        $operator = $stageDef->onActivate($cloner, $staging);

        // 走完 Session 初始化的流程. 然后听取当前消息.
        return new ReincarnationOperator(
            $cloner->runtime->trace,
            $operator,
            new InputFlow()
        );
    }
}