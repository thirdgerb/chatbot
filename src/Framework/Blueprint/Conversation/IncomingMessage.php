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

use Commune\Message\Blueprint\ConvoMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read ConvoMsg $message
 * @property-read MessageScope $scope
 * @property-read string $userId
 */
interface IncomingMessage
{
    public function getMessage() : ConvoMsg;

    public function getChatScope() : ChatScope;

}