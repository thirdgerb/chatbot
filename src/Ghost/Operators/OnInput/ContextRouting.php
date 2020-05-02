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
use Commune\Blueprint\Ghost\Routes\Intercept\Intend;
use Commune\Ghost\Operators\FlowOperator;
use Commune\Ghost\Operators\Stage\Interception;
use Commune\Ghost\Operators\Traits\TIntentMatcher;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ContextRouting extends FlowOperator
{
    use TIntentMatcher;
    
    public function doInvoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();
        $intentNames = $process->contextRoutes;
        if (empty($intentNames)) {
            return null;
        }

        $mind = $cloner->mind;
        $contextReg = $mind->contextReg();

        foreach ($intentNames as $intent) {

            if (! $contextReg->hasDef($intent)) {
                continue;
            }

            $matched = $this->wildcardIntentMatch($intent, $cloner)
                ?? $this->exactIntentMatch($intent, $cloner);

            if (empty($matched)) {
                continue;
            }

            // 返回 Interception
            return new Interception(
                $cloner->findTask($matched),
                $matched,
                Intend::class
            );
        }
        return null;
    }


}