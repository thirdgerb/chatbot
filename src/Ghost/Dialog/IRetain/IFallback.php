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
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Blueprint\Ghost\Dialog\Retain\Fallback;
use Commune\Ghost\Dialog\DialogHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IFallback extends AbsDialogue implements Fallback
{
    protected function runInterception(): ? Dialog
    {
        return null;
    }

    protected function runTillNext(): Dialog
    {
        return DialogHelper::retain($this);
    }

    protected function selfActivate(): void
    {
        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl);
    }


}