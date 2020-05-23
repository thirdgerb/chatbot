<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog;

use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\Operator\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IActivate extends AbsDialog implements Activate
{
    protected function runTillNext() : Operator
    {
        $this->getProcess()->unsetWaiting($this->_ucl);

        $stageDef = $this->_ucl->findStageDef($this->_cloner);
        return $stageDef->onActivate($this);
    }
}