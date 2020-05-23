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

use Commune\Blueprint\Ghost\Dialog\Resume;
use Commune\Blueprint\Ghost\Operate\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IResume extends AbsDialog implements Resume
{
    protected function runTillNext() : Operator
    {
        $this->getProcess()->unsetWaiting($this->ucl);

        $stageDef = $this->_ucl->findStageDef($this->cloner);
        return $stageDef->onResume($this);
    }
}