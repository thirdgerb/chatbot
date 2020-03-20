<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Redirection\Process;

use Commune\Chatbot\Ghost\Blueprint\Ghost;
use Commune\Chatbot\Ghost\Blueprint\Redirector;
use Commune\Chatbot\Ghost\Redirection\AbsRedirector;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Confuse extends AbsRedirector
{
    public function invoke(Ghost $ghost): ? Redirector
    {


        // 1. 如果有父进程, 则由父进程 fallback 进行处理

        // 2. 如果没有父进程, 则拒绝应答用户, 并发送拒答响应, 重置对话.
    }


}