<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\Temp;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Ghost\Dialog\IActivate\IStaging;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GoNext extends AbsDialog
{
    protected function runTillNext(): Dialog
    {
        // 检查是哪一种.
        $process = $this->getProcess();

        $next = $process->shiftPath($this->ucl->getContextId());
        if (isset($next)) {
            $ucl = $this->ucl->goStage($next)->toInstance($this->cloner);
            return new IStaging($this->cloner, $ucl, $this->popStack());
        }

        // fulfill
        $next = new GoFulfill($this->cloner, $this->ucl, $this->popStack());
        return $next->withPrev($this->prev);
    }

    protected function selfActivate(): void
    {
    }


}