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
 * 回调一个进程时执行.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Fallback extends AbsRedirector
{
    public function invoke(Ghost $ghost): ? Redirector
    {
        // 1. 让当前 stage 的 fallback 方法进行处理.
    }


}