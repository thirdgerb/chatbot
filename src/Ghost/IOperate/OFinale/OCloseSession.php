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
use Commune\Message\Host\SystemInt\SessionQuitInt;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OCloseSession extends AbsFinale
{

    protected function toNext(): Operator
    {
        $this->dialog
            ->send()
            ->message(new SessionQuitInt($this->dialog->ucl->toEncodedStr()))
            ->over();

        $this->dialog->cloner->quit();
        return $this;
    }
}