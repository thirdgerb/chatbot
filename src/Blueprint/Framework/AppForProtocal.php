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

use Commune\Support\Protocal\Protocal;
use Commune\Support\Protocal\ProtocalMatcher;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface AppForProtocal
{

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