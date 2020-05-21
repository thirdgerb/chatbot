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
use Commune\Ghost\Dialog\AbsBaseDialog;
use Commune\Blueprint\Ghost\Dialog\Withdraw\Quit;
use Commune\Ghost\Dialog\DialogHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IQuit extends AbsBaseDialog implements Quit
{

    protected function runInterception(): ? Dialog
    {
        return null;
    }

    protected function runTillNext(): Dialog
    {
        $process = $this->getProcess();

        return $this->withdrawCanceling($this, $process, Quit::class)
            ?? $this->withdrawBlocking($this, $process, Quit::class)
            ?? $this->withdrawSleeping($this, $process, Quit::class)
            ?? $this->withdrawWatching($this, $process, Quit::class)
            ?? DialogHelper::newDialog(
                $this,
                $this->ucl,
                Dialog\Finale\CloseSession::class
            );
    }


    protected function selfActivate(): void
    {
        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl);
    }


}