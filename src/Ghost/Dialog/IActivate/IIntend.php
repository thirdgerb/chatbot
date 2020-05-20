<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IActivate;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Activate\Intend;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\DialogHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IIntend extends AbsDialogue implements Intend
{
    const SELF_STATUS = self::INTEND;

    protected function runInterception(): ? Dialog
    {
        return DialogHelper::intercept($this);
    }

    protected function runTillNext(): Dialog
    {
        return DialogHelper::activate($this);
    }

    protected function selfActivate(): void
    {
        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl->toEncodedStr());
    }


}