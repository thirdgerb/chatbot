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

use Commune\Blueprint\Kernel\Protocols\GhostRequest;
use Commune\Blueprint\Kernel\Protocols\GhostResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneRoutePipe extends AClonePipe
{
    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        // 无状态请求不需要浪费精力做路由
        // 异步请求不允许被动创建路由. 异步请求可能是任何一方发过来的 async input
        if ($this->cloner->isStateless() || $request->isAsync()) {
            return $next($request);
        }

        // 如果是机器人自己发送的消息, 也不处理.
        // 不过这通常就是 async message 啊?
        $shellName = $request->getFromApp();
        if ($this->cloner->getAppId() === $shellName) {
            return $next($request);
        }

        // 设置路由关系.
        $storage = $this->cloner->storage;
        $routes = $storage->shellSessionRoutes ?? [];
        $routes[$shellName] = $request->getFromSession();

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