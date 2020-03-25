<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Routing;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Operator\Operator;


/**
 * 重定向到其它的 Context
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Redirect
{
    public function sleepTo() : Operator;

    public function dependOn() : Operator;

    /**
     * 将当前 Thread 撤出, 等待服务回调.
     *
     * @param string $serviceName
     * @param array $payload
     * @param null|string|Context $toContext
     * @return Operator
     */
    public function yieldTo(
        string $serviceName,
        array $payload,
        $toContext = null
    ) : Operator;
}