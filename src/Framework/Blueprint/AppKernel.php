<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint;

use Commune\Framework\Blueprint\Server\Request;
use Commune\Framework\Blueprint\Server\Response;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface AppKernel
{
    /**
     * 同步请求.
     *
     * @param Request $request
     * @param Response $response
     * @param array $middleware
     */
    public function handleRequest(
        Request $request,
        Response $response,
        array $middleware
    ) : void;

    /**
     * 异步请求
     *
     * @param Request $request
     * @param Response $response
     * @param array $middleware
     */
    public function asyncHandleRequest(
        Request $request,
        Response $response,
        array $middleware
    ) : void;

    /**
     * 异步响应.
     *
     * @param Request $request
     * @param Response $response
     * @param array $middleware
     */
    public function asyncHandleResponse(
        Request $request,
        Response $response,
        array $middleware
    ) : void;
}