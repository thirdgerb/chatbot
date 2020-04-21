<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Convo;

use Psr\Log\LoggerInterface;


/**
 * Ghost 的日志. 应当记录 IncomingMsg 的 Scope
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ConvoLogger extends LoggerInterface
{
}