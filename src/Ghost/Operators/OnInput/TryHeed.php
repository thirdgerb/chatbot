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
use Commune\Blueprint\Ghost\Routes\React\Heed;
use Commune\Ghost\Operators\FlowOperator;
use Commune\Ghost\Operators\Stage\Reaction;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TryHeed extends FlowOperator
{
    public function doInvoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();
        $current = $process->currentTask();

        return new Reaction(
            $current,
            $current->fullStageName(),
            Heed::class
        );
    }

}