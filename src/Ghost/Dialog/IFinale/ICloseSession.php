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
use Commune\Message\Host\SystemInt\SessionQuitInt;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ICloseSession extends AbsDialog implements Finale
{
    protected function runTillNext(): Dialog
    {
        $this->send()->message(new SessionQuitInt());

        $this->cloner->quit();

        $this->ticked = true;
        return $this;
    }
}