<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts\Messenger;

use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;

/**
 * 消息广播, 最廉价的异构解决方案.
 * 当 N 个 Shell 连接到同一个 Ghost 的时候,
 * 一个 Shell 的输入消息只有在特定条件下, 才需要发送到其它 Shell.
 *
 * 如果是 Ghost 主动推送, 则难免会淹没在极其复杂的推送规则中, 无法作为项目的通用解决方案.
 *
 * 因此, 相比之下广播是更为适用的方案.
 * 只广播关于 BatchId 相关的信息, 然后由 Shell 自己组装 ShellOutputRequest.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Broadcaster
{

    /**
     * 广播一个响应.
     *
     * @param GhostRequest $request
     * @param GhostResponse $response
     * @param array $routes         路由表, ShellId => ShellSessionId
     */
    public function publish(
        GhostRequest $request,
        GhostResponse $response,
        array $routes
    ) : void;

    /**
     * @param callable $callback
     *
     * 传入参数 (string $chan, ShellOutputRequest $request)
     * 
     * $chan = 监听的渠道. 可以是 shell/shellId (shell/shellId), 
     * 也可能是 shell/null (shell)
     * 
     * $request = 从服务端广播过来的 request 
     *
     * @param string $shellId
     * @param string|null $shellSessionId
     */
    public function subscribe(
        callable $callback,
        string $shellId,
        string $shellSessionId = null
    ) : void;

}