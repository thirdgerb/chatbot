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
use Commune\Ghost\Dialog\AbsWithdraw;
use Commune\Blueprint\Ghost\Dialog\Withdraw\Quit;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IQuit extends AbsWithdraw implements Quit
{


    protected function runTillNext(): Dialog
    {
        $process = $this->getProcess();

        return $this->withdrawCanceling($process)
            ?? $this->quitBlocking($process)
            ?? $this->quitSleeping($process)
            ?? $this->quitWatching($process)
            ?? $this->closeSession();
    }


    protected function selfActivate(): void
    {
        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl);
    }


}