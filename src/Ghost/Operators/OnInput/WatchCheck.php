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
use Commune\Blueprint\Ghost\Routes\Intercept\Watch;
use Commune\Ghost\Operators\FlowOperator;
use Commune\Ghost\Operators\Stage\Interception;
use Commune\Ghost\Operators\Traits\TIntentMatcher;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class WatchCheck extends FlowOperator
{
    use TIntentMatcher;

    public function doInvoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();

        $watching = $process->watching;
        if (empty($watching)) {
            return null;
        }

        // 遍历 Watching
        foreach ($watching as $taskId => $stages) {
            $task = $process->getTask($taskId);

            // 遍历 Watching Task 的 Watch Stages
            foreach ($stages as $stageName) {
                $fullName = $task->fullStageName($stageName);

                $matched = $this->wildcardIntentMatch($fullName, $cloner)
                    ?? $this->exactIntentMatch($fullName, $cloner);

                if (!$matched) {
                    continue;
                }

                return new Interception(
                    $task,
                    $matched,
                    Watch::class
                );
            }
        }

        return null;
    }


}