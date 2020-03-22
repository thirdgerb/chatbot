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

use Carbon\Carbon;

/**
 * 可以在 Platform 传输的消息, 需要从 Request 中获取, 或者渲染
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ConvoMsg extends Message
{
    /**
     * 是否是空消息
     * @return bool
     */
    public function isEmpty() : bool;


}