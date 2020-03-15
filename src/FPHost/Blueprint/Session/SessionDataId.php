<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\FPHost\Blueprint\Session;

use Commune\FPHost\Blueprint\Session;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SessionDataId
{
    public function id() : string;

    public function type() : string;

    public function toSessionData(Session $session) : SessionData;
}