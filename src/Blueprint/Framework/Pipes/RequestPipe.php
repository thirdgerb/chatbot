<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework\Pipes;

use Closure;
use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Framework\Request\AppResponse;

/**
 * App 处理请求的管道.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RequestPipe
{
    const HANDLER_FUNC = 'handle';

    public function handle(AppRequest $request, Closure $next) : AppResponse;

}