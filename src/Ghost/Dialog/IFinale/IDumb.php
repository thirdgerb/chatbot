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
use Commune\Blueprint\Ghost\Dialog\Finale\Dumb;
use Commune\Ghost\Dialog\AbsDialogue;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IDumb extends AbsDialogue implements Dumb
{
    protected function runInterception(): ? Dialog
    {
        return null;
    }

    protected function runTillNext(): Dialog
    {
        return $this;
    }

    protected function selfActivate(): void
    {
        $this->cloner->noState();
    }


}