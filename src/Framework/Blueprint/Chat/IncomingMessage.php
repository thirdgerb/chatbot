<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Chat;

use Commune\Messages\Blueprint\ConvoMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface IncomingMessage
{
    public function getMessage() : ConvoMsg;

    public function getChatScope() : ChatScope;

}