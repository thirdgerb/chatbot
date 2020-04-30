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
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Routes\React\Heed;
use Commune\Ghost\Operators\Stage\Reaction;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StageRouting extends Runner
{
    public function invoke(Cloner $cloner): ? Operator
    {
        $stageRoutes = $this->process->stageRoutes;

        if (empty($stageRoutes)) {
            return null;
        }

        $current = $this->process->currentTask();

        foreach ($stageRoutes as $stageName) {

            $fullname = $current->fullStageName($stageName);

            $matched = $this->wildcardIntentMatch($fullname, $cloner)
                ?? $this->exactIntentMatch($fullname, $cloner);

            if (empty($matched)) {
                continue;
            }

            $current = $this->process->currentTask();
            return new Reaction(
                $current,
                Heed::class
            );
        }

        return null;
    }




}