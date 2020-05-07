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
use Commune\Blueprint\Ghost\Dialog\Activate\Fallback;
use Commune\Ghost\Dialog\AbsDialogue;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IFallback extends AbsDialogue implements Fallback
{
    protected function runInterception(): ? Dialog
    {
    }

    protected function runTillNext(): Dialog
    {
    }

    protected function selfActivate(): void
    {
    }


}