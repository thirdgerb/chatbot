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

use Commune\Blueprint\Host;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface App
{
    /**
     * 是否调试状态.
     * @return bool
     */
    public function isDebugging() : bool;

    /**
     * @return Host
     */
    public function getHost() : Host;

    /**
     * 创建一个请求级容器, 并添加默认的绑定
     * @param string $id
     * @return ReqContainer
     */
    public function newReqContainerInstance(string $id) : ReqContainer;


}