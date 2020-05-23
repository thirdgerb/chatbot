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

use Commune\Blueprint\Ghost\Dialog\Retain;
use Commune\Blueprint\Ghost\Operator\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRetain extends AbsDialog implements Retain
{
    protected function runTillNext() : Operator
    {
        $this->getProcess()->unsetWaiting($this->ucl);

        $stageDef = $this->ucl->findStageDef($this->cloner);
        return $stageDef->onRetain($this);
    }
}