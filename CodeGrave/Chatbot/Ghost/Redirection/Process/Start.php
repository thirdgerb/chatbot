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
 * 启动一个进程时执行.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Start extends AbsRedirector
{
    public function invoke(Ghost $ghost): ? Redirector
    {
        // 1. 先尝试匹配已知的意图, 作为不同的分支路线. 这些分支路线是可以动态扩展的.

        // 2. 查看是否有子进程, 如果有子进程, 则让子进程去处理请求.

        // 3. 如果没有子进程, 则生成一个 FallbackProcess
    }


}