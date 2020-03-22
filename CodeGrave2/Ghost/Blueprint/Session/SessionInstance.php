<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Session;

use Illuminate\Contracts\Session\Session;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SessionInstance
{

    public function isInstanced() : bool;

    /**
     * @param Session $session
     * @return static
     */
    public function toInstance(Session $session) : SessionInstance;

}