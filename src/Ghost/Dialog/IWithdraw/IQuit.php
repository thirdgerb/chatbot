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
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Ghost\Dialog\AbsWithdraw;
use Commune\Blueprint\Ghost\Dialog\Withdraw\Quit;
use Commune\Ghost\Dialog\Traits\TWithdrawFlow;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IQuit extends AbsDialog implements Quit
{
    use TWithdrawFlow;


    protected function runTillNext(): Dialog
    {
        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl);


        $depending = $process->popDepending($this->ucl->getContextId());
        if (!empty($depending)) {
            $process->addCanceling($depending);
        }

        return $this->withdrawCanceling($process)


    }


    protected function selfActivate(): void
    {
        $this->runStack();

        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl);

    }


}