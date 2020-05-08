<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IRedirect;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\IActivate\IRedirectTo;
use Commune\Ghost\Dialog\IActivate\IStaging;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class INext extends AbsDialogue
{
    protected function runInterception(): ? Dialog
    {
        return null;
    }

    protected function runTillNext(): Dialog
    {
        $process = $this->getProcess();
        $nextStr = $process->popPath();

        // 没有下一步的话, 则等于 fulfill.
        if (empty($nextStr)) {
            return $this->fulfillTo();
        }

        $to = $process->decodeUcl($nextStr);
        if ($this->ucl->getContextId() === $to->getContextId()) {
            $next = new IStaging($this->cloner, $to, []);
        } else {
            $next = new IRedirectTo($this->cloner, $to, []);
        }

        return $next->withPrev($this);
    }

    protected function selfActivate(): void
    {
        // unset
        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl);
    }


}