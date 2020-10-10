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

use Commune\Blueprint\Kernel\Protocols\AppRequest;
use Commune\Blueprint\Kernel\Protocols\AppResponse;
use Commune\Blueprint\Kernel\Protocols\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocols\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocols\ShellOutputResponse;
use Commune\Support\Utils\TypeUtils;


/**
 * Swoole Async Shell 上的广播 Adapter
 * 通常用于测试. 会把所有的消息广播到 Shell 所有的连接.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwlBroadcastAdapter extends SwlDuplexTextShellAdapter
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


    public function sendResponse(AppResponse $response): void
    {
        if (!$this->isValidResponse($response)) {
            $type = TypeUtils::getType($response);
            $this->packer->fail("invalid response, $type given");
            return;
        }

        $sessionId = $response->getSessionId();
        $output = $this->serializeResponse($response);
        $this->send($sessionId, $output);
    }


    protected function send(string $sessionId, string $data): void
    {
        if (empty($data)) {
            return;
        }

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