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

use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Dialog\IActivate;
use Commune\Blueprint\Ghost\Dialog\Activate\Preempt;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IPreempt extends IActivate implements Preempt
{

    protected function runTillNext(): Operator
    {
        $process = $this->getProcess();
        $process->unsetWaiting($this->_ucl);

        $awaiting = $process->getAwaiting();
        if (isset($awaiting)) {
            $priority = $awaiting->findContextDef($this->_cloner)->getPriority();
            $process->addBlocking($awaiting, $priority);
        }

        $stageDef = $this->_ucl->findStageDef($this->_cloner);

        return $stageDef->onActivate($this);

    }
}