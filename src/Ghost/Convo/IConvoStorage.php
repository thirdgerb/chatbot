<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Convo;

use Commune\Blueprint\Ghost\Convo\ConvoStorage;
use Commune\Framework\Session\AStorage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IConvoStorage extends AStorage implements ConvoStorage
{
    public function getSessionKey(string $sessionName, string $sessionId): string
    {
        return "ghost:$sessionName:id:$sessionId:storage";
    }


}