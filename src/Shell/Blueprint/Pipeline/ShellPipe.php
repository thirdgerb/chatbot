<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Pipeline;

use Commune\Shell\Blueprint\Session\ShlSession;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellPipe
{
    const HANDLER = 'handle';

    public function handle(ShlSession $session, callable $next) : ShlSession;
}