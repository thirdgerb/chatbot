<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint\Dialog;

use Commune\Chatbot\Ghost\Blueprint\Dialog;
use Commune\Chatbot\Ghost\Blueprint\Redirection\GoBackward;
use Commune\Chatbot\Ghost\Blueprint\Redirection\GoContext;
use Commune\Chatbot\Ghost\Blueprint\Redirection\GoExit;

/**
 * 当前 Stage 作为全局意图被命中时.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface OnIntending extends Dialog, GoContext, GoBackward, GoExit
{
}