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

use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Ghost\Dialog\Finale\Await;
use Commune\Blueprint\Ghost\Runtime\Operator;

/**
 * 多轮对话导航逻辑.
 * 管理语境和对话节点的跳转.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Navigator
{

    /*-------- finale --------*/

    /**
     * 等待用户的回复.
     *
     * @param array $allowContexts
     * @param array $stageRoutes
     * @param int|null $expire
     * @return Await
     */
    public function await(
        array $allowContexts = [],
        array $stageRoutes = [],
        int $expire = null
    ) : Await;


    /**
     * 重置到上一轮的对话.
     * 可以发出消息, 或者不发出.
     *
     * @param bool $silent
     * @return Operator
     */
    public function rewind(bool $silent = false) : Operator;

    /**
     * 重新启动当前 stage
     * @return Operator
     */
    public function reactivate() : Operator;

    /**
     * 什么也没听见, 当本轮对话没有发生.
     * @return Operator
     */
    public function dumb() : Operator;

    /**
     * 退回到若干步之前.
     * @param int $step
     * @return Operator
     */
    public function backStep(int $step = 1) : Operator;

    /**
     * 主动强调无法理解当前对话.
     * 不会继续尝试 Wake 其它对话.
     * @return Operator
     */
    public function confuse() : Operator;


    /*-------- retrace --------*/

    /**
     * 完成当前语境, 并将当前语境回调.
     * 同时指定一个可能的下阶段语境.
     *
     * @param Ucl|null $target      如果为 null, 默认是当前 context
     * @param int $gcTurns
     * @param array $restoreStages
     * @return Operator
     */
    public function fulfill(
        Ucl $target = null,
        int $gcTurns = 0,
        array $restoreStages = []
    ) : Operator;

    /**
     * 终止当前语境, 会触发 withdraw 流程.
     *
     * @param Ucl|null $target      如果为 null, 默认是当前 context
     * @return Operator
     */
    public function cancel(
        Ucl $target = null
    ) : Operator;

    /**
     * 拒绝进入当前语境, 会触发 withdraw 流程.
     * @param Ucl|null $target      如果为 null, 默认是当前 context
     * @return Operator
     */
    public function reject(
        Ucl $target = null
    ) : Operator;

    /**
     * 当前语境失败
     * @param Ucl|null $target      如果为 null, 默认是当前 context
     * @return Operator
     */
    public function fail(
        Ucl $target = null
    ) : Operator;

    /**
     * 尝试退出当前多轮对话, 会触发 withdraw 流程.
     * @return Operator
     */
    public function quit() : Operator;


    /*-------- redirect --------*/


    /**
     * 重置当前 context 的 stage 路径.
     * @return Navigator
     */
    public function clearPath() : Navigator;

    /**
     * @param string ...$stageNames
     * @return Operator
     */
    public function next(string ...$stageNames) : Operator;

    /**
     * 经过若干 stage, 然后回到当前节点.
     *
     * @param string $stageName
     * @param string ...$stageNames
     * @return Operator
     */
    public function circle(string $stageName, string ...$stageNames) : Operator;

    /**
     * 重定向到另一个 Ucl,
     * @param Ucl $target
     * @return Operator
     */
    public function redirectTo(Ucl $target) : Operator;

    /**
     * 返回到指定的 ucl (或默认的ucl), 然后清空所有的 waiting 关系.
     * @param Ucl|null $root
     * @return Operator
     */
    public function reset(Ucl $root = null) : Operator;

    /*-------- self waiting --------*/

    /**
     * 依赖一个目标 Context. 当目标 Context fulfill 时,
     * 会调用当前 Stage 的 onFulfill 方法.
     *
     * @param Ucl $dependUcl
     * @param string|null $fieldName
     * @return Operator
     */
    public function dependOn(Ucl $dependUcl, string $fieldName = null) : Operator;

    /**
     * 将自己压入 block 状态, 然后进入 $to 语境.
     *
     * @param Ucl $target
     * @return Operator
     */
    public function blockTo(Ucl $target) : Operator;

    /**
     * 让当前 Context 进入 sleep 状态
     *
     * @param string[] $wakenStages  指定这些 Stage, 可以在匹配意图后主动唤醒.
     * @param Ucl|string|null $target
     * @return Operator
     */
    public function sleepTo(Ucl $target, array $wakenStages = []) : Operator;


    /*-------- 让语境进入 waiting 状态 --------*/

    /**
     * @param Ucl $subject
     * @return Navigator
     */
    public function watch(Ucl $subject) : Navigator;

    /**
     * @param Ucl $subject
     * @param array $wakeStages
     * @return Navigator
     */
    public function sleep(Ucl $subject, array $wakeStages = []) : Navigator;

    /**
     * @param Ucl $subject
     * @param int|null $priority
     * @return Navigator
     */
    public function block(Ucl $subject, int $priority = null) : Navigator;

}