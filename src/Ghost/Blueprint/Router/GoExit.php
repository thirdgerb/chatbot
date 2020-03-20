<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Router;

use Commune\Ghost\Blueprint\Redirector;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GoExit
{
    /**
     * 退出会话
     * @return Redirector
     */
    public function quit() : Redirector;

    /**
     * 退出当前的 Thread, 可以被 intended stage 拦截.
     * @return Redirector
     */
    public function cancel() : Redirector;

    /**
     * 结束当前的 Context, 回调 intended stage
     * @return Redirector
     */
    public function fulfill() : Redirector;

}