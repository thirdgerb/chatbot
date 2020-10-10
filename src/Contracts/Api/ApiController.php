<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts\Api;

use Commune\Blueprint\Kernel\Protocols\AppRequest;
use Commune\Blueprint\Kernel\Protocols\AppResponse;
use Commune\Protocols\HostMsg\ApiMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ApiController
{
    public function __invoke(AppRequest $request, ApiMsg $message) : AppResponse;
}