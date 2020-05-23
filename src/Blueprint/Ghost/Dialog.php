<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Operate;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Tools\Finale;
use Commune\Blueprint\Ghost\Operate\Hearing;
use Commune\Blueprint\Ghost\Tools\Caller;
use Commune\Blueprint\Ghost\Tools\Deliver;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read Cloner $cloner
 * @property-read Context $context
 * @property-read Ucl $ucl              当前 Dialog 的 Context 地址.
 * @property-read Dialog|null $prev     前一个 Dialog
 * @property-read int $depth
 */
interface Dialog
{
    /*----- dialog event -----*/

    // 任意 dialog
    const ANY           = Dialog::class;

    // 进入一个Stage 时触发的事件.
    const ACTIVATE      = Dialog\Activate::class;
    // 由用户消息触发的事件.
    const RECEIVE       = 1;
    // stage 回调时
    const RESUME        = Dialog\Resume::class;
    // stage 退出时
    const WITHDRAW      = Dialog\Withdraw::class;
    // 由意图触发的事件. 均可拦截.
    const INTEND        = Dialog\Intend::class;
    // cancel : depending, blocking, fallback -> sleeping, watch
    const CANCEL        = Dialog\Withdraw\Cancel::class;
    // quit : depending, blocking, fallback -> sleeping, watch
    const QUIT          = Dialog\Withdraw\Quit::class;


    /* activate 激活一个 stage */

    // 相同的 context 里一个 stage 进入另一个 stage
    const STAGING       = Dialog\Activate\Staging::class;
    // 一个 context 依赖到另一个 context 时
    const DEPENDED      = Dialog\Activate\Depend::class;
    // 返回到根路径.
    const RESET         = Dialog\Activate\Reset::class;
    // reactivate
    const REACTIVATE    = Dialog\Activate\Reactivate::class;

    /* receive 直接由用户消息触发的事件. */

    // watch
    const WATCH         = Dialog\Activate\Watch::class;
    // await -> heed
    const HEED          = Dialog\Retain\Heed::class;
    // confuse : fallback -> sleeping, watch
    const CONFUSE       = Dialog\Withdraw\Confuse::class;

    /* retain 中断的 stage 得到回调 */

    // sleep -> wake
    const WAKE          = Dialog\Retain\Wake::class;
    // depending -> callback
    const CALLBACK      = Dialog\Retain\Callback::class;
    // dying -> restore
    const RESTORE       = 1;
    // yielding -> retain
    const RETAIN        = 2;
    // 一个 blocking context 重新占据对话
    const PREEMPT       = Dialog\Activate\Preempt::class;

    /* withdraw */

    /**
     * Dialog 自身所代表的事件类型
     *
     * @param string $statusType
     * @return bool
     */
    public function isEvent(string $statusType) : bool ;

    /*----- call -----*/

    /**
     * 上下文相关的 IoC 容器.
     * @return Caller
     */
    public function caller() : Caller;


    /*----- conversation -----*/

    /**
     * 发送消息给用户
     * @return Deliver
     */
    public function send() : Deliver;


    /*----- memory -----*/

    /**
     * 获取一个记忆体.
     * @param string $name
     * @return Recollection
     */
    public function recall(string $name) : Recollection;

    /*----- 上下文状态管理. -----*/

    /**
     * 等待用户回复
     * @return Operate\Await
     */
    public function await() : Operate\Await;

    /**
     * @param string ...$stageNames
     * @return Operator
     */
    public function next(string ...$stageNames) : Operator;

    /**
     * 重定向到另一个 Context.
     *
     * @param Ucl $ucl
     * @return Operator
     */
    public function redirectTo(Ucl $ucl) : Operator;


    /**
     * 返回到指定的 ucl (或默认的ucl), 然后清空所有的 waiting 关系.
     * @param Ucl|null $root
     * @return Operator
     */
    public function reset(Ucl $root = null) : Operator;

    /**
     * 完成当前语境.
     *
     * @param array $restoreStage
     * @param int $gcTurns
     * @return mixed
     */
    public function fulfill(
        array $restoreStage = [],
        int $gcTurns = 0
    ) : Operator;

    /**
     * 退出当前语境.
     * @return Operator
     */
    public function cancel() : Operator;

    /**
     * 退出整个对话
     * @return Operator
     */
    public function quit() : Operator;


    /**
     * 同一个 Context 内部的场景切换.
     *
     * @return Operate\Staging
     */
    public function staging() : Operate\Staging;

    /**
     * 对输入的消息进行链式匹配操作.
     * @return Hearing
     */
    public function hearing() : Operate\Hearing;

    /**
     * 挂起当前语境, 跳转到其它语境.
     * @return Operate\Suspend
     */
    public function suspend() : Operate\Suspend;


}