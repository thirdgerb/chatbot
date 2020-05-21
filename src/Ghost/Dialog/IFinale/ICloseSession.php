<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IFinale;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Blueprint\Ghost\Dialog\Finale;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ICloseSession extends AbsDialog implements Finale
{
    protected function runTillNext(): Dialog
    {
        $this->ticked = true;
        return $this;
    }

    protected function selfActivate(): void
    {
        $this->runStack();
        $this->cloner->quit();
    }

}