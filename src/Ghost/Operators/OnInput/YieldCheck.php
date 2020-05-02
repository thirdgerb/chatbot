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
use Commune\Protocals\Intercom\YieldMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class YieldCheck extends FlowOperator
{
    public function doInvoke(Cloner $cloner): ? Operator
    {
        $input = $cloner->ghostInput;
        if (!$input instanceof YieldMsg) {
            return null;
        }

        $process = $cloner->runtime->getCurrentProcess();
        $task = $input->toTask($cloner);
        $loser = $process->challenge($task);

        // 挑战成功
        if (isset($loser)) {
            $process->blockTask($loser);
            return new Activation($task, Retain::class);
        }

        // 挑战失败, 自己放到 block 栈中.
        $process->blockTask($task);
        return null;
    }

}