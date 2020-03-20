<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Redirection\Process;

use Commune\Chatbot\Ghost\Blueprint\Ghost;
use Commune\Chatbot\Ghost\Blueprint\Redirector;
use Commune\Chatbot\Ghost\Redirection\AbsRedirector;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Wait extends AbsRedirector
{
    public function invoke(Ghost $ghost): ? Redirector
    {
    }


}