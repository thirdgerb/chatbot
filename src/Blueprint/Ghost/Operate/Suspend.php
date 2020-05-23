<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Operate;
use Commune\Blueprint\Ghost\Ucl;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Suspend
{
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



}