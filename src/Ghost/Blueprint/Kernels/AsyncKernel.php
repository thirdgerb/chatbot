<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Kernels;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface AsyncKernel
{

    /**
     * 尝试处理一条消息.
     * @return bool 处理到了消息
     */
    public function onMessage() : bool;
}