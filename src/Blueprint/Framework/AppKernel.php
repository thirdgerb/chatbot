<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework;

use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Support\Protocal\Protocal;
use Commune\Support\Protocal\ProtocalMatcher;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface AppKernel
{
    /*------ request ------*/

    /**
     * 运行一个状态机, 直到给出预期的结果, 否则抛出异常.
     *
     * @param AppRequest $request
     * @param string $expect        预期的返回类型.
     * @param int $turns
     * @return AppResponse
     */
    public function handleRequest(
        AppRequest $request,
        string $expect,
        int $turns = 0
    ) : AppResponse;


    /*------ protocal ------*/

    /**
     * 协议的匹配器.
     * @return ProtocalMatcher
     */
    public function getProtocalMatcher() : ProtocalMatcher;

    /**
     * 遍历定义的协议, 轮流获取可能的 handler
     *
     * 建议所有的协议 handler 都应该是一个 callable 对象.
     *
     * @param ReqContainer $container
     * @param Protocal $protocal
     * @param string|null $handlerInterface
     * @return \Generator  $handlerInterface[]
     */
    public function eachProtocalHandler(
        ReqContainer $container,
        Protocal $protocal,
        string $handlerInterface = null
    ) : \Generator;



}