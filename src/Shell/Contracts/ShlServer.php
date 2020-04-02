<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Contracts;

use Commune\Framework\Contracts\Server;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShlServer extends Server
{
    /**
     * 关闭会话.
     */
    public function closeSession() : void;

}