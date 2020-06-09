<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Commands\Super;

use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Cmd\AGhostCmd;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RedirectCmd extends AGhostCmd
{
    const SIGNATURE = 'redirect 
       {name : 目标 context 名称}
    ';

    const DESCRIPTION = '指定重定向到一个目标 context';

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $name = $message['name'] ?? '';

        $reg = $this->cloner->mind->contextReg();
        if (empty($name) || !$reg->hasDef($name)) {
            $this->error("context $name not found");
            return;
        }

        $message = Ucl::make($name)->findContext($this->cloner)->toContextMsg();
        $this->cloner->input->setMessage($message);
        $this->goNext();
    }


}