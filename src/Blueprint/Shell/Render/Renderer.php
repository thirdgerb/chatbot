<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Shell\Render;

use Commune\Protocols\HostMsg;


/**
 * 可以是进程级单例, 也可以是请求级单例.
 * 都会由请求级容器来实例化. 适合自行定义.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Renderer
{

    /**
     * @param HostMsg $message
     * @return HostMsg[]
     */
    public function __invoke(HostMsg $message) : ? array;

}