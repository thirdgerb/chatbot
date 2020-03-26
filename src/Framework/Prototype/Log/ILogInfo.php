<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Log;

use Commune\Framework\Contracts\LogInfo;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ILogInfo implements LogInfo
{
    /*-------- boot --------*/



    /*-------- shell --------*/

    public function shellReceiveInvalidRequest(string $message): string
    {
        // TODO: Implement shellReceiveInvalidRequest() method.
    }

    public function shellDirectiveNotExists(string $directiveId): string
    {
        // TODO: Implement directiveNotExists() method.
    }


}