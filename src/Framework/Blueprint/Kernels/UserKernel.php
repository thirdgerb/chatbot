<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Kernel;

use Commune\Shell\Platform\Request;
use Commune\Shell\Platform\Response;

/**
 * 面向用户的 kernel
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface UserKernel
{

    /**
     * 同步接受一个用户的消息, 并发送一个同步的响应.
     *
     * @param Request $request
     * @param Response $response
     */
    public function onMessage(
        Request $request,
        Response $response
    ) : void;

    /**
     * 检查消息管道, 发送一个同步的响应.
     *
     * @param Response $response
     */
    public function onResponse(
        Response $response
    ) : void;

    /**
     * 在双工服务器上, 进行主动在线推送.
     */
    public function startDuplexPush() : void;

    /**
     * 进行离线推送.
     */
    public function startOfflinePush() : void;



}