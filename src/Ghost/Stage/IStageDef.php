<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Stage;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Activate;
use Commune\Blueprint\Ghost\Dialog\Receive;
use Commune\Blueprint\Ghost\Dialog\Resume;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;


/**
 * 标准的 stage 定义.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IStageDef extends AStageDef
{
    public function onActivate(Activate $dialog): Operator
    {
        return $this->fireEvent($dialog)
            ?? $dialog
                ->send()
                ->info($this->name)
                ->over()
                ->await();
    }

    public function onReceive(Receive $dialog): Operator
    {
        return $this->fireEvent($dialog) ?? $dialog->confuse();
    }

    public function onRedirect(Dialog $prev, Ucl $current): ? Operator
    {
        return $this->fireRedirect($prev);
    }

    public function onResume(Resume $dialog): ? Operator
    {
        return $this->fireEvent($dialog);
    }


}