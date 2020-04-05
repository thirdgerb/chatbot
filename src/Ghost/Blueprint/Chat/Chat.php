<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Chat;

use Commune\Framework\Blueprint\Session\Session;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Chat
{
    public function getChatId() : string;

    public function getSessionId() : string;

    public function getScope() : ChatScope;

    public function resetSession() : void;

    public function lock() : bool;

    public function unlock() : bool;

    public function setScope(ChatScope $scope) : void;

    public function save(Session $session) : void;
}