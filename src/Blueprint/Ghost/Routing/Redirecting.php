<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Routing;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Operator\Operator;


/**
 * 重定向到其它的 Context
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Redirecting
{
    /**
     * 将当前的 Thread 睡眠掉.
     * 用一个 Context, 或者唤醒一个 Thread (优先 blocking )来处理后续流程.
     * 如果当前 Thread 就是唯一的 Thread, 则会触发 Quit
     *
     * @param Context|null $to
     * @param int $gcTurn  当前 Thread 不是 sleep, 而是进入 GC 周期, 除非被唤醒, 否则消失.
     * @return Operator
     */
    public function sleepTo(Context $to = null, int $gcTurn = 0) : Operator;

    /**
     * 依赖一个 Context, 该 Context 回调时会触发 onReject/onCancel/onFulfill 等状态.
     * @param Context $depending
     * @return Operator
     */
    public function depend(Context $depending) : Operator;

    /**
     * @param Context $context
     * @return Operator
     */
    public function watch(Context $context) : Operator;

    /**
     * 将当前 Thread 暂时撤出, 等待服务回调.
     *
     * @param string $serviceName
     * @param Context $context
     * @param int|null $expire
     * @return Operator
     */
    public function yieldTo(
        string $serviceName,
        Context $context,
        int $expire = null
    ) : Operator;

    /**
     * 替换掉当前的 Thread, 进入一个新的 Thread
     * @param Context $context
     * @return Operator
     */
    public function replaceTo(Context $context) : Operator;

}