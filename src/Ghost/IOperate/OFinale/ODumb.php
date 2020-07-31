<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\OFinale;

use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\IOperate\AbsOperator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ODumb extends AbsOperator
{
    protected function toNext(): Operator
    {
        // 对话进程首次接受的消息很可能是 event, 这时就不要dumb 了.
        if (!$this->dialog->process->isFresh()) {
            $this->dialog->cloner->noState();
        }
        return $this;
    }


}