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
interface GoBreak
{

    /**
     * 等待用户响应
     * @return Redirector
     */
    public function wait() : Redirector;

    /**
     * 表示无法处理用户的响应.
     * @return Redirector
     */
    public function confuse() : Redirector;

    /**
     * 重置为上一轮对话的终态
     * @return Redirector
     */
    public function rewind() : Redirector;

    /**
     * 返回到上上轮对话的终态
     * @return Redirector
     */
    public function backStep() : Redirector;



}