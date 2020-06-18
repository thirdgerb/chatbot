<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\ClonePipes;

use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneRoutePipe extends AClonePipe
{
    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        if ($this->cloner->isStateless()) {
            return $next($request);
        }

        // 设置路由关系.
        $storage = $this->cloner->storage;
        $routes = $storage->shellSessionRoutes ?? [];
        $shellName = $request->getInput()->getShellName();
        $routes[$shellName] = $request->getSessionId();
        $storage->shellSessionRoutes = $routes;

        $response = $next($request);

        // 会话结束的话, 终止当前 shell 的路由关系.
        if ($this->cloner->isConversationEnd()) {
            unset($routes[$shellName]);
            $storage->shellSessionRoutes = $routes;
        }

        return $response;
    }


}