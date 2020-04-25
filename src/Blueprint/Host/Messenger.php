<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Host;

use Commune\Blueprint\Host\Messenger\Broadcast;
use Commune\Blueprint\Host\Messenger\MessageDB;


/**
 * 公共的消息管理器.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Messenger
{
    /**
     * @return MessageDB
     */
    public function getDB() : MessageDB;

    /**
     * @return Broadcast
     */
    public function getBroadcast() : Broadcast;

}