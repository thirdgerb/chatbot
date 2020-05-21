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
use Commune\Blueprint\Ghost\Dialog\Withdraw\Fail;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IFail extends AbsWithdraw implements Fail
{
    protected function runTillNext(): Dialog
    {
        $process = $this->getProcess();
        $process->addCanceling([$this->ucl]);

        return $this->withdrawCanceling($process)
            ?? $this->fallbackFlow($process);
    }

    protected function selfActivate(): void
    {
        $this->runStack();
    }

}