<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\OExiting;

use Commune\Blueprint\Ghost\Runtime\Task;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\IOperate\Flows\FallbackFlow;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OCancel extends AbsExiting
{

    protected function toNext(): Operator
    {
        $target = $this->dialog->ucl;
        $process = $this->dialog->process;

        $this->addCanceling($target);
        return $this->recursiveWithdraw($process)
            ?? $this->fallback();
    }

    protected function fallback() : Operator
    {
        return new FallbackFlow($this->dialog);
    }

    protected function getWithdrawWatcher(Task $task): ? Ucl
    {
        return $task->watchCancel();
    }


}