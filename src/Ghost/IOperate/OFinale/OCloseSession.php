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
use Commune\Message\Host\Convo\IContextMsg;
use Commune\Message\Host\SystemInt\SessionQuitInt;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OCloseSession extends AbsFinale
{

    protected function toNext(): Operator
    {
        if (!$this->cloner->isSubProcess()) {
            $this->dialog
                ->send()
                // 发送退出消息
                ->message(SessionQuitInt::instance($this->dialog->ucl))
                // 清空状态
                ->message(new IContextMsg([]))
                ->over();
        }
        $this->cloner->endConversation();
        return $this;
    }
}