<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Chat;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Chat
{
    public function getId() : string;

    public function getChatQueue() : ChatQueue;

    public function getChatScope() : ChatScope;

    public function lock(ChatScope $chatInfo) : bool;

    public function unlock(ChatScope $chatInfo) : bool ;
}