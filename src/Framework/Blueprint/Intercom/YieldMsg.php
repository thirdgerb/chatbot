<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Intercom;

use Commune\Message\Blueprint\Message;


/**
 * 只在内部传输的特殊消息.
 * 将一个 Context 传递到另一个对话里.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface YieldMsg extends Message
{
    public function getContextName() : string;

    public function getEntities() : array;

    public function getThreadId() : string;

    /*------ dimension ------*/

    /**
     * 所属的对话 CloneId
     * @return string
     */
    public function getYieldFrom() : string;

}
