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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SessionInstance
{

    public function isInstanced() : bool;

    /**
     * @param GhtSession $session
     * @return static
     */
    public function toInstance(GhtSession $session) : SessionInstance;

}