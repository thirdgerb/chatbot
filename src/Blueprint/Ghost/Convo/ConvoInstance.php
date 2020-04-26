<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Convo;

/**
 * 需要在对话中二次实例化的对象.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ConvoInstance
{

    /**
     * 以已经执行了实例化
     * @return bool
     */
    public function isInstanced() : bool;

    /**
     * @return ConvoStub
     */
    public function toConvoStub() : ConvoStub;
}