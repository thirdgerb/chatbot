<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Kernel;

use Commune\Framework\Blueprint\Server\Request;
use Commune\Framework\Blueprint\Server\Response;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Kernel
{
    /**
     * 同步请求.
     *
     * @param Request $request
     * @param Response $response
     * @param bool $noState
     */
    public function onSyncRequest(
        Request $request,
        Response $response,
        bool $noState = false
    ) : void;

    /**
     * 异步请求
     *
     * @param Request $request
     * @param Response $response
     * @param bool $noState
     */
    public function onAsyncRequest(
        Request $request,
        Response $response,
        bool $noState = false
    ) : void;

    /**
     * 异步响应.
     *
     * @param Request $request
     * @param Response $response
     * @param bool $noState
     */
    public function onAsyncResponse(
        Request $request,
        Response $response,
        bool $noState = false
    ) : void;
}