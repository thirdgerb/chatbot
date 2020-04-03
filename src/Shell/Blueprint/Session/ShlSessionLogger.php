<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Session;

use Commune\Framework\Blueprint\Session\SessionLogger;

/**
 * Shell 的请求级日志, 应当记录下 Request 的 logContext
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShlSessionLogger extends SessionLogger
{

}