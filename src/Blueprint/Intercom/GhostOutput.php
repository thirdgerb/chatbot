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
interface GhostOutput
{
    /**
     * 标记消息是哪一次请求中产生的.
     * @return string
     */
    public function getTraceId() : string;

    /**
     * 对于 Ghost 而言的 分身 Id
     * @return string
     */
    public function getCloneId() : string;


    /**
     * @return ShellMessage[]
     */
    public function getOutputs() : array;

}