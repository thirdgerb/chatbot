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

        $depending = $process->popDepending($this->ucl->getContextId());
        if (!empty($depending)) {
            $process->addCanceling($depending);
        }



    }


    protected function selfActivate(): void
    {
        $this->runStack();

        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl);

    }


}