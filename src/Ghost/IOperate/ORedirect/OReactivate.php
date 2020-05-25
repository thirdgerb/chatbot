<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\ORedirect;

use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Dialog\IActivate\IReactivate;
use Commune\Ghost\IOperate\AbsOperator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OReactivate extends AbsOperator
{
    protected function toNext(): Operator
    {
        $ucl = $this->dialog->ucl;

        $reactivate = new IReactivate($this->dialog, $ucl);
        $this->dialog->process->activate($ucl);
        return $ucl
            ->findStageDef($this->dialog->cloner)
            ->onActivate($reactivate);

    }


}