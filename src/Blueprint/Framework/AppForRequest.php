<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework;

use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface AppForRequest
{
    /*------ request ------*/

    /**
     * @param AppRequest $request
     * @return AppResponse
     */
    public function handleRequest(AppRequest $request) : AppResponse;


}