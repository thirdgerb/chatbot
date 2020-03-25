<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Abstracted;

use Commune\Message\Message;

/**
 * 默认回复
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DefaultReplies
{
    public function addReply(Message $message) : void;

    public function getReplies() : array;

}