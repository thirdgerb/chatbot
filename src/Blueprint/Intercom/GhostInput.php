<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Intercom;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostInput
{
    /**
     * 标记消息是哪一次请求中产生的.
     * @return string
     */
    public function getTraceId() : string;

    public function getCloneId() : string;

    public function getShellMessage() : ShellMessage;
}