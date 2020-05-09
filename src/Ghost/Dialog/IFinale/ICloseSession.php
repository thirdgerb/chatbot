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
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Blueprint\Ghost\Dialog\Finale\CloseSession;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ICloseSession extends AbsDialogue implements CloseSession
{
    const SELF_STATUS = self::FINALE;

    protected function runInterception(): ? Dialog
    {
        return null;
    }

    protected function runTillNext(): Dialog
    {
        $this->ticked = true;
        return $this;
    }

    protected function selfActivate(): void
    {
        $this->cloner->setSessionExpire(0);
    }


}