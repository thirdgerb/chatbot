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
 * 当前 Process 有关的操作.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface OnProcess
{

    /**
     * 返回 Process 的起点. 清空所有的 Threads
     *
     * @return Redirector
     */
    public function home() : Redirector;


}