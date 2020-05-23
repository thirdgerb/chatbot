<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IWithdraw;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Runtime\Operator;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Ghost\Dialog\AbsWithdraw;
use Commune\Blueprint\Ghost\Dialog\Withdraw\Quit;
use Commune\Ghost\Dialog\Traits\TFallbackFlow;
use Commune\Ghost\Dialog\Traits\TQuitFlow;
use Commune\Ghost\Dialog\Traits\TWithdrawFlow;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IQuit extends AbsWithdraw implements Quit
{
    protected function runTillNext(): Operator
    {
        $process = $this->getProcess();
        $process->unsetWaiting($this->_ucl);

        $process->addCanceling([$this->_ucl]);

        return $this->withdrawCanceling($process)
            ?? $this->quitBatch($process, $process->eachCallbacks())
            ?? $this->quitBatch($process, $process->eachBlocking())
            ?? $this->quitBatch($process, $process->eachSleeping())
            ?? $this->quitBatch($process, $process->eachYielding())
            ?? $this->quitBatch($process, $process->eachWatchers())
            ?? $this->quitSession();
    }
}