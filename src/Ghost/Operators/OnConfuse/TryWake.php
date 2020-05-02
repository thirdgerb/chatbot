<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\OnConfuse;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Ghost\Operators\FlowOperator;
use Commune\Ghost\Operators\Traits\TIntentMatcher;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TryWake extends FlowOperator
{
    use TIntentMatcher;

    protected function doInvoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();

        $sleeping = $process->sleeping;

        foreach($sleeping as $taskId => $wakenStages) {
            $task = $process->getTask($taskId);
            
            foreach ($wakenStages as $stageName) {
                $fullName = $task->fullStageName($stageName);
                
                $matched = $this->wildcardIntentMatch($fullName, $cloner)
                    ?? $this->exactIntentMatch($fullName, $cloner);
                
            }

        }
    }


}