<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework\Handlers;

use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Protocals\HostMsg\Convo\ApiMsg;


/**
 * 处理 Api 消息的控制器.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ApiController
{

    public function __invoke(AppRequest $request, ApiMsg $message) : AppResponse;

}