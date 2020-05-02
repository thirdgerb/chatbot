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
use Commune\Blueprint\Ghost\Routes\Activate\Retain;
use Commune\Ghost\Operators\FlowOperator;
use Commune\Ghost\Operators\Stage\Activation;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Challenge extends FlowOperator
{
    public function doInvoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();
        $blocking = $process->popBlocking();

        if (!isset($blocking)) {
            return null;
        }

        $loser = $process->challenge($blocking);
        if (isset($loser)) {
            $process->blockTask($loser);
            return new Activation(
                $blocking,
                $blocking->fullStageName(),
                Retain::class
            );
        }

        // 输了自己回去
        $process->blockTask($blocking);
        return null;
    }


}