<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts\Redis;

use Redis;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RedisConnection
{

    /**
     * 从连接池里获取实例.
     * @return Redis
     */
    public function get();

    /**
     * 将 redis 实例返回到连接池.
     */
    public function release() : void;
}