<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IRetain;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Blueprint\Ghost\Dialog\Retain\Restore;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRestore extends AbsDialog implements Restore
{

    protected function runTillNext() : Dialog
    {
        $stageDef = $this->ucl->findStageDef($this->cloner);
        return $stageDef->onRetain($this);
    }

    protected function selfActivate(): void
    {
        $this->runStack();
        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl);
    }


}