<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\Flows;

use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\IOperate\AbsOperator;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Operate\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class FallbackFlow extends AbsOperator
{
    protected function toNext(): Operator
    {
        $process = $this->dialog->process;

        return $this->redirect($process->firstCallback())
            ?? $this->redirect($process->firstBlocking())
            ?? $this->redirect($process->firstSleeping())
            ?? $this->fallbackToRoot($process);
    }

    protected function redirect(Ucl $target = null) : ? Operator
    {
        if (isset($target)) {
            return $this->dialog->redirectTo($target);
        }

        return null;
    }

    protected function fallbackToRoot(Process $process) : Operator
    {
        $root = $process->getRoot();

        // 如果不是相同的, 回到 root
        if (!$root->isSameContext($this->dialog->ucl)) {
            return $this->dialog->reset();
        }

        // 否则 quit
        return $this->dialog->quit();
    }

}