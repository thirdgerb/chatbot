<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Cloner;

use Commune\Blueprint\Ghost\Cloner\ClonerStorage;
use Commune\Framework\Session\ASessionStorage;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IClonerStorage extends ASessionStorage implements ClonerStorage
{
    public function getSessionKey(string $sessionName, string $sessionId): string
    {
        return "ghost:$sessionName:id:$sessionId:storage";
    }


}