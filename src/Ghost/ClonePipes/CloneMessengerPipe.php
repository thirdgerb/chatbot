<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\ClonePipes;

use Closure;
use Commune\Blueprint\Exceptions\Runtime\BrokenRequestException;
use Commune\Blueprint\Exceptions\Runtime\BrokenSessionException;
use Commune\Blueprint\Exceptions\Runtime\QuitSessionException;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Message\Host\SystemInt\RequestFailInt;
use Commune\Message\Host\SystemInt\SessionQuitInt;
use Commune\Protocals\HostMsg\Convo\UnsupportedMsg;
use Commune\Blueprint\Framework\Request\AppResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneMessengerPipe extends AClonePipe
{

    protected function doHandle(GhostRequest $request, Closure $next) : GhostResponse
    {
        $message = $request->getInput()->getMessage();

        if ($message instanceof UnsupportedMsg) {
            return $request->fail(AppResponse::NO_CONTENT);
        }

        try {

            return $next($request);

        } catch (BrokenSessionException $e) {
            // 退出会话.
            $this->cloner->quit();
            return $request->response($this->cloner);

        } catch (BrokenRequestException $e) {

            return $request->output(new RequestFailInt());
        }

    }
}