<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Run;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Definition\StageDef;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Routes\Intercept\Watch;
use Commune\Blueprint\Ghost\Snapshot\Task;
use Commune\Ghost\Operators\Stage\Interception;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class WatchCheck extends Runner
{
    public function invoke(Cloner $cloner): ? Operator
    {
        $watching = $this->process->watching;
        if (empty($watching)) {
            return null;
        }

        $stageReg = $cloner->mind->stageReg();

        // 遍历 Watching
        foreach ($watching as $taskId => $stages) {
            $task = $this->process->getTask($taskId);

            // 遍历 Watching Task 的 Watch Stages
            foreach ($stages as $stageName) {
                $fullName = $task->fullStageName($stageName);

                if (StringUtils::isWildCardPattern($fullName)) {

                }

                $stageDef = $stageReg->getDef($fullName);

                // 用 intentDef 去匹配
                $intentDef = $stageDef->asIntentDef();

                if ($intentDef->validate($cloner)) {
                    $current = $this->process->currentTask();
                    return new Interception(
                        $stageDef,
                        $current,
                        $task,
                        Watch::class
                    );
                }
            }
        }

        return null;
    }

    protected function matchStageRoutes(
        Cloner $cloner,
        Task $task,
        array $stages
    ) : ? Operator
    {

        $stageReg = $cloner->mind->stageReg();

        // 遍历 Watching Task 的 Watch Stages
        foreach ($stages as $stageName) {

            $fullName = $task->fullStageName($stageName);

            $matched = $this->wildcardIntentMatch($fullName, $cloner)
                ?? $this->exactIntentMatch($fullName, $cloner);

            if (empty($matched)) {
                continue;
            }

            $stageDef = $stageReg->getDef($matched);
            $current = $this->process->currentTask();
            return new Interception(
                $stageDef,
                $current,
                $task,
                Watch::class
            );
        }

        return null;
    }

}