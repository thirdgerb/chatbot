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
use Commune\Blueprint\Ghost\Routing\Staging;
use Commune\Ghost\Operators\FlowOperator;
use Commune\Ghost\Operators\Stage\Activation;
use Commune\Ghost\Operators\Traits\TIntentMatcher;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StageRouting extends FlowOperator
{
    use TIntentMatcher;

    public function doInvoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();
        $stageRoutes = $process->stageRoutes;

        if (empty($stageRoutes)) {
            return null;
        }

        $current = $process->currentTask();

        foreach ($stageRoutes as $stageName) {

            $fullname = $current->fullStageName($stageName);

            $matched = $this->wildcardIntentMatch($fullname, $cloner)
                ?? $this->exactIntentMatch($fullname, $cloner);

            if (empty($matched)) {
                continue;
            }

            $current->changeStage($matched);
            return new Activation(
                $current,
                Staging::class
            );
        }

        return null;
    }




}