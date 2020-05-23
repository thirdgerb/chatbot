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

use Commune\Ghost\Dialog\AbsDialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Dialog\Activate\Reset;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IReset extends AbsDialog implements Reset
{

    protected function runTillNext() : Operator
    {
        $process = $this->getProcess();
        // 重置对话状态
        $process->flushWaiting();

        $stageDef = $this->ucl->findStageDef($this->cloner);
        return $stageDef->onActivate($this);
    }

}