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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IDumb extends AbsDialog
{
    protected function runTillNext(): Dialog
    {
        $this->cloner->noState();

        $this->ticked = true;
        return $this;
    }
}