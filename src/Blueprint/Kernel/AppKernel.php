<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Kernel;

use Commune\Blueprint\Kernel\Protocols\AppRequest;
use Commune\Blueprint\Kernel\Protocols\AppResponse;
use Commune\Support\Protocol\Protocol;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Support\Protocol\ProtocolMatcher;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface AppKernel
{

    /**
     * 运行一个请求, 获得响应.
     *
     * @param AppRequest $Protocol
     * @param string|null $interface
     * @param ReqContainer|null $container
     * @return AppResponse
     */
    public function handleRequest(
        AppRequest $Protocol,
        string $interface = null,
        ReqContainer $container = null
    ) : AppResponse;


    /*------ Protocol ------*/

    /**
     * 协议的匹配器.
     * @return ProtocolMatcher
     */
    public function getProtocolMatcher() : ProtocolMatcher;

    /**
     * 遍历定义的协议, 轮流获取可能的 handler
     *
     * 建议所有的协议 handler 都应该是一个 callable 对象.
     *
     * @param ReqContainer $container
     * @param Protocol $Protocol
     * @param string|null $handlerInterface
     * @return \Generator  $handlerInterface[]
     */
    public function eachProtocolHandler(
        ReqContainer $container,
        Protocol $Protocol,
        string $handlerInterface = null
    ) : \Generator;

    /**
     * 获取第一个 handler . 所有的handler 都应该是 callable 对象.
     * @param ReqContainer $container
     * @param Protocol $Protocol
     * @param string|null $handlerInterface
     * @return callable|null
     */
    public function firstProtocolHandler(
        ReqContainer $container,
        Protocol $Protocol,
        string $handlerInterface = null
    ) : ? callable;
}