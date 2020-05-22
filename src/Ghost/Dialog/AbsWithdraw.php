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

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Withdraw;
use Commune\Ghost\Dialog\Traits\TFallbackFlow;
use Commune\Ghost\Dialog\Traits\TWithdrawFlow;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsWithdraw extends AbsDialog implements Withdraw
{
    use TWithdrawFlow, TFallbackFlow;

    protected function runTillNext(): Dialog
    {
        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl);
        $process->addCanceling([$this->ucl]);

        return $this->withdrawCanceling($process)
            ?? $this->fallbackFlow($process);
    }

}