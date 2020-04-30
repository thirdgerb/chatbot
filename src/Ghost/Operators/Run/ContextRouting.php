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
use Commune\Blueprint\Ghost\Routes\Intercept\Intend;
use Commune\Ghost\Operators\Stage\Interception;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ContextRouting extends Runner
{
    public function invoke(Cloner $cloner): ? Operator
    {
        $intentNames = $this->process->contextRoutes;
        $mind = $cloner->mind;
        $contextReg = $mind->contextReg();
        $stageReg = $mind->stageReg();

        foreach ($intentNames as $intent) {

            if (! $contextReg->hasDef($intent)) {
                continue;
            }

            $matched = $this->wildcardIntentMatch($intent, $cloner)
                ?? $this->exactIntentMatch($intent, $cloner);

            $stageDef = $stageReg->getDef($matched);
            return new Interception(
                $stageDef,
                $this->process->currentTask(),
                // 从语境中获得新的 task 对象.
                $cloner->getContext($matched),
                // intend
                Intend::class
            );

        }
    }


}