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
use Commune\Blueprint\Ghost\Dialog\Withdraw\Reject;
use Commune\Ghost\Dialog\AbsBaseDialog;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IReject extends AbsBaseDialog implements Reject
{
    protected function runInterception(): ? Dialog
    {
        return null;
    }

    protected function runTillNext(): Dialog
    {
        $process = $this->getProcess();

        return $this->withdrawCanceling($this, $process, Reject::class)
            ?? $this->fallbackFlow($this, $process);
    }

    protected function selfActivate(): void
    {
        $process = $this->getProcess();
        $process->addCanceling([$this->ucl]);
    }


}