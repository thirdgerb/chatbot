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

use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Dialog\AbsWithdraw;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IFulfill extends AbsWithdraw
{
    protected function runTillNext(): Operator
    {
        $process = $this->getProcess();
        $depending = $process->dumpDepending($this->_ucl->getContextId());

        if (!empty($depending)) {
            $process->addCallback(...$depending);
        }

        return $this->fallbackFlow($process);
    }


}