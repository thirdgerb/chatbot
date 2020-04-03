<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Session;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface SessionPipe
{
    const HANDLER = 'handle';

    public function handle(Session $session, callable $next) : Session;

}