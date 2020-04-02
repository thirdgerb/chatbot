<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Pipeline;

use Commune\Shell\Blueprint\Session\ShlSession;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ResponsePipe extends AShellPipe
{
    public function doHandle(ShlSession $session, callable $next): ShlSession
    {
        /**
         * @var ShlSession $session
         */
        $session = $next($session);

        // buffer response
        $outputs = $session->getShellOutputs();
        $response = $session->response;
        $response->buffer($outputs);
        return $session;
    }


}