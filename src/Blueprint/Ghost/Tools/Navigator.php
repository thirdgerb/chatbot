<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Tools;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Ghost\Dialog\Finale\Await;



/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Navigator
{

    /*-------- 链式调用 --------*/

    public function watch(Ucl $subject) : Navigator;
    public function sleep(Ucl $subject, array $wakeStages = []) : Navigator;
    public function block(Ucl $subject, int $priority = 1) : Navigator;
    public function kill(Ucl $subject, int $gcTurns = 0, array $restoreStages = []) : Navigator;

    /*-------- finale --------*/

    /**
     * 等待用户的回复.
     *
     * @param string[] $stageInterceptors
     * @param array $contextInterceptors
     * @param int|null $expire
     * @return Await
     */
    public function await(
        array $stageInterceptors = [],
        array $contextInterceptors = [],
        int $expire = null
    ) : Await;

    /**
     * 重置到上一轮的对话.
     * @param bool $silent
     * @return Dialog
     */
    public function rewind(bool $silent = false) : Dialog;

    /**
     * 什么也没听见, 当本轮对话没有发生.
     * @return Dialog
     */
    public function dumb() : Dialog;

    /**
     * 退回到若干步之前.
     * @param int $step
     * @return Dialog
     */
    public function backStep(int $step = 1) : Dialog;

    /**
     * 主动强调无法理解当前对话.
     * 不会继续尝试 Wake 其它对话.
     * @return Dialog
     */
    public function confuse() : Dialog;


    /*-------- retrace --------*/

    /**
     * 完成当前语境, 并将当前语境回调.
     * 同时指定一个可能的下阶段语境.
     *
     * @param int $gcTurns
     * @param array $restoreStages
     * @return Dialog
     */
    public function fulfill(int $gcTurns = 0, array $restoreStages = []) : Dialog;

    /**
     * 终止当前语境, 会触发 withdraw 流程.
     * @return Dialog
     */
    public function cancel() : Dialog;

    /**
     * 拒绝进入当前语境, 会触发 withdraw 流程.
     * @return Dialog
     */
    public function reject() : Dialog;

    /**
     * @return Dialog
     */
    public function fail() : Dialog;

    /**
     * 尝试退出当前多轮对话, 会触发 withdraw 流程.
     * @return Dialog
     */
    public function quit() : Dialog;




    /*-------- redirect --------*/

    /**
     * 进入到相同 Context 下的另一个 stage
     *
     * @param string $stageName
     * @return Dialog
     */
    public function toStage(string $stageName) : Dialog;

    /**
     * 重定向到另一个 Ucl,
     * @param Ucl $target
     * @return Dialog
     */
    public function redirectTo(Ucl $target) : Dialog;

    /**
     * 返回到指定的 ucl (或默认的ucl), 然后清空所有的 waiting 关系.
     * @param Ucl|null $root
     * @return Dialog
     */
    public function home(Ucl $root = null) : Dialog;

    /*-------- self waiting --------*/


    /**
     * @param Ucl $on
     * @return Dialog
     */
    public function watchOn(Ucl $on) : Dialog;

    /**
     * 依赖一个目标 Context. 当目标 Context fulfill 时,
     * 会调用当前 Stage 的 onFulfill 方法.
     *
     * @param Ucl $depend
     * @param string $fieldName
     * @return Dialog
     */
    public function dependOn(Ucl $depend, string $fieldName) : Dialog;

    /**
     * 将自己压入 block 状态, 然后进入 $to 语境.
     *
     * @param Ucl $to
     * @return Dialog
     */
    public function blockTo(Ucl $to) : Dialog;

    /**
     * 让当前 Context 进入 sleep 状态
     *
     * @param string[] $wakenStages  指定这些 Stage, 可以在匹配意图后主动唤醒.
     * @param Ucl|null $to
     * @return Dialog
     */
    public function sleepTo(Ucl $to = null, array $wakenStages = []) : Dialog;

    /**
     * 依赖一个目标 Context.
     * 但目标 Context 不是在当前 Cloner 里启动,
     * 而是异步提交给另一个 shell 的 guest 去处理.
     * 当目标 Context 结束调用时, 会再异步传输到当前 Cloner 里,
     * 调用当前 Stage 的 onFulfill 方法.
     *
     * 是异步任务的标准实现方法.
     *
     * @param string $shellName
     * @param string $guestId
     * @param Ucl|null $fallbackTo
     * @return Dialog
     */
    public function yieldTo(
        string $shellName,
        string $guestId,
        Ucl $fallbackTo = null
    ) : Dialog;



}