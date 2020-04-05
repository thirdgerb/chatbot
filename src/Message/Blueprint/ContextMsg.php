<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint;

/**
 * 用于同步当前的 Context.
 * 如果端上能对 Context 作出响应, 则有必要添加该 Message.
 * Ghost 也应该对 Context 消息作出响应, 强制改变状态.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ContextMsg extends Message
{
    /**
     * @return string
     */
    public function getContextName() : string;

    /**
     * @return array
     */
    public function getEntities() : array;

}