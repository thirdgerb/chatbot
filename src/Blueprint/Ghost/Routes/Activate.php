<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Routes;

use Commune\Blueprint\Ghost\Routing\Awaiting;
use Commune\Blueprint\Ghost\Routing\Redirecting;

/**
 * 启动一个 Stage
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Activate extends
    Route,
    Awaiting,
    Redirecting
{
}