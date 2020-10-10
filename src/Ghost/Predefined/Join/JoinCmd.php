<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Predefined\Join;

use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Kernel\GhostCmd\AGhostCmd;
use Commune\Protocols\HostMsg\Convo\ContextMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class JoinCmd extends AGhostCmd
{

    const SIGNATURE = 'join {session : 目标会话的 session id}';

    const DESCRIPTION = '申请加入另一个 session ';

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $session = $message['session'] ?? '';
        if (empty($session)) {
            $this->error("session is required");
            return;
        }

        $context = JoinRequest::genUcl([
            'session' => $session,
            'fallback' => ''
        ])->findContext($this->cloner);


        $message = $context->toContextMsg()->withMode(ContextMsg::MODE_REDIRECT);
        $this->cloner->input->setMessage($message);

        $this->info("尝试申请加入会话 $session");
        $this->goNext();
    }


}