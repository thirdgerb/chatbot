<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Shell\Tcp;

use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Platform\Libs\SwlAsync\TcpAdapterAbstract;


/**
 * Swoole Async Shell 上的广播 Adapter
 * 通常用于测试. 会把所有的消息广播到 Shell 所有的连接.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwlAsyncBroadcastAdapter extends TcpAdapterAbstract
{

    protected function isValidRequest(AppRequest $request): bool
    {
        return $request instanceof ShellInputRequest
            || $request instanceof ShellOutputRequest;
    }

    protected function isValidResponse(AppResponse $response): bool
    {
        return $response instanceof ShellOutputResponse;
    }

    protected function send(string $sessionId, string $data): void
    {
        // 广播消息.
        $server = $this->packer->server;
        $platform = $this->packer->platform;

        foreach ($server->connections as $fd) {
            if ($server->exists($fd)) {
                $server->send($fd, $data);

            } else {
                $sessionId = $platform->getFdSession($fd);
                if (isset($sessionId)) {
                    $this->packer->platform->unsetSessionRoute($sessionId);
                }
            }
        }

    }

}