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
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RetainMsg extends Message
{
    public function getContextName() : string;

    public function getEntities() : array;

    /*------ dimension ------*/

    public function getThreadId() : string;

    /**
     * 所属的对话 CloneId
     * @return string
     */
    public function getRetainFrom() : string;
}