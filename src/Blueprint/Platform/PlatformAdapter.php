<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Platform;

use Commune\Blueprint\Shell\Requests\ShellRequest;
use Commune\Blueprint\Shell\Responses\ShellResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface PlatformAdapter
{
    public function getRequest() : ShellRequest;

    public function sendResponse() : ShellResponse;
}