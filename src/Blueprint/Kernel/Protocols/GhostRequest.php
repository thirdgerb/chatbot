<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Kernel\Protocols;

use Commune\Protocols\HostMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostRequest extends AppRequest, HasInput
{

    /**
     * 是否是异步的消息.
     * 如果 GhostRequest 是异步的, 有以下的情况 :
     *
     * 1. 锁定的策略不同. 异步输入消息不会因为锁而丢弃, 而会重回队列.
     * 2. 如果是异步消息, 输出时会广播给所有的 shell. 而同步消息不会给输入的 shell 做广播.
     *
     * @return bool
     */
    public function isAsync() : bool;

    /**
     * 异步的投递消息, 通过 clone 再传递给 shell.
     * @return bool
     */
    public function isDelivery() : bool;

    /**
     * @return bool
     */
    public function isStateless() : bool;

    /**
     * 请求来自的 app
     * @return string
     */
    public function getFromApp() : string;

    /**
     * 请求来自的 sessionId
     * @return string
     */
    public function getFromSession() : string;

    /**
     * 变更路由到的对象 sessionId
     * 替换 Input 的 sessionId, 但保留 from session
     * @param string $sessionId
     */
    public function routeToSession(string $sessionId) : void;


    /**
     * @param int $errcode
     * @param string $errmsg
     * @return GhostResponse
     */
    public function response(int $errcode = AppResponse::SUCCESS, string $errmsg = '') : GhostResponse;

    /**
     * @param string $appId
     * @param string $appName
     * @param HostMsg $message
     * @param HostMsg ...$messages
     * @return GhostResponse
     */
    public function output(
        string $appId,
        string $appName,
        HostMsg $message, HostMsg ...$messages
    ) : GhostResponse;
}