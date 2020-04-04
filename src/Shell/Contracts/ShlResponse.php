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

use Commune\Framework\Blueprint\Intercom\ShellMsg;
use Commune\Framework\Blueprint\Server\Response;

/**
 * 负责向 Shell 的客户端发送响应的模块
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShlResponse extends Response
{

    /*--------- 发送响应 ----------*/

    /**
     * 增加一个需要同步回复的消息.
     * @param ShellMsg[] $messages
     */
    public function buffer(array $messages) : void;

}