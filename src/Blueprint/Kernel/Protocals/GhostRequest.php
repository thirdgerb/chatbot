<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Kernel\Protocals;

use Commune\Protocals\HostMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostRequest extends AppRequest, InputRequest
{

    /**
     * @return bool
     */
    public function isAsync() : bool;

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