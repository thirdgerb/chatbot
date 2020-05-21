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
use Commune\Ghost\Dialog\AbsBaseDialog;
use Commune\Blueprint\Ghost\Dialog\Activate\Redirect;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRedirect extends AbsBaseDialog implements Redirect
{
    protected function runTillNext(): Dialog
    {
        $stageDef = $this->ucl->findStageDef($this->cloner);
        return $stageDef->onActivate($this);
    }

    protected function selfActivate(): void
    {
        $this->runStack();
    }


}