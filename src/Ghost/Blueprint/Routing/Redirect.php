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
    public function sleepTo(
        Context $to = null,
        int $gc = 0
    ) : Operator;

    /**
     * @param Context $depending
     * @return Operator
     */
    public function dependOn(
        Context $depending
    ) : Operator;

    /**
     * 将当前 Thread 撤出, 等待服务回调.
     * @param Context $asyncContext
     * @param Context|null $wakeContext
     * @return Operator
     */
    /**
     * @param Context $asyncContext
     * @param Context|null $wakeContext
     * @param int $expire 过期时间
     * @return Operator
     */
    public function yieldTo(
        Context $asyncContext,
        Context $wakeContext = null,
        int $expire
    ) : Operator;

    /**
     * @param Context $context
     * @return Operator
     */
    public function replaceThread(Context $context) : Operator;

    /**
     * @param Context $context
     * @return Operator
     */
    public function replaceProcess(Context $context) : Operator;

    /**
     * @return Operator
     */
    public function home() : Operator;
}