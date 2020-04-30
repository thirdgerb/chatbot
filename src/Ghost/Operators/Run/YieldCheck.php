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
use Commune\Blueprint\Ghost\Routes\Activate\Retain;
use Commune\Ghost\Operators\Stage\Activation;
use Commune\Protocals\Intercom\YieldMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class YieldCheck extends Runner
{
    public function invoke(Cloner $cloner): ? Operator
    {
        if (!$this->input instanceof YieldMsg) {
            return null;
        }

        $task = $this->input->toTask($cloner);
        $loser = $this->process->challenge($task);

        // 挑战成功
        if (isset($loser)) {
            $this->process->blockTask($loser);
            return new Activation($task, Retain::class);
        }

        // 挑战失败, 自己放到 block 栈中.
        $this->process->blockTask($task);
        return null;
    }

}