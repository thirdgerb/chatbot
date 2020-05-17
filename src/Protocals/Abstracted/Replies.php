<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Abstracted;

use Commune\Protocals\HostMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Replies
{
    public function addReplies(HostMsg ...$message) : void;

    public function hasReplies() : bool;

    /**
     * @return HostMsg[]
     */
    public function getReplies() : ? array;
}