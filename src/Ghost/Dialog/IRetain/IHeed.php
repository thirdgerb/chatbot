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
use Commune\Blueprint\Ghost\Dialog\Retain\Heed;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IHeed extends AbsDialog implements Heed
{

    protected function runTillNext(): Dialog
    {
        $stageDef = $this->ucl->findStageDef($this->cloner);
        return $stageDef->onRetain($this);
    }

    protected function selfActivate(): void
    {
        $this->runStack();
        $this->getProcess()->unsetWaiting($this->ucl);
    }


}