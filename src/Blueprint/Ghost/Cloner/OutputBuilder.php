<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Cloner;

use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\GhostMsg;


/**
 * 输出消息的构建器.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface OutputBuilder
{

    /*------- 生成消息 -------*/

    /**
     * 得到一个 GhostMsg 实例.
     * @param HostMsg $message
     * @return GhostMsg
     */
    public function withMessage(HostMsg $message) : GhostMsg;

}