<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Redirection;

use Commune\Chatbot\Ghost\Blueprint\Dialog;
use Commune\Chatbot\Ghost\Blueprint\Redirector;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsRedirector implements Redirector
{

    public function ticking(): int
    {
    }

    public function dialog(): Dialog
    {
    }


}