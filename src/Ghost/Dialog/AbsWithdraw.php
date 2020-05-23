<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog;

use Commune\Blueprint\Ghost\Dialog\Withdraw;
use Commune\Blueprint\Ghost\Runtime\Operator;
use Commune\Ghost\Dialog\Traits\TFallbackFlow;
use Commune\Ghost\Dialog\Traits\TQuitFlow;
use Commune\Ghost\Dialog\Traits\TWithdrawFlow;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsWithdraw extends AbsDialog implements Withdraw
{
    use TWithdrawFlow, TFallbackFlow, TQuitFlow;

    protected function runTillNext(): Operator
    {
        $process = $this->getProcess();
        $process->unsetWaiting($this->_ucl);

        $process->addCanceling([$this->_ucl]);

        return $this->withdrawCanceling($process)
            ?? $this->fallbackFlow($process)
            ?? $this->quitWatching($process)
            ?? $this->quitSession();
    }

}