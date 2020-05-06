<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Runtime;

use Commune\Blueprint\Ghost\Ucl;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * Task 是 Context 在进程中的运行状态缓存.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Ucl $ucl
 * @property array $memory      Context 的 Session 级别的上下文记忆.
 *
 * @property-read string[]|null $callbackUcl
 */
interface Task extends ArrayAndJsonAble
{
//    /*----- status -----*/
//
//    // 闲置状态, 可以被激活.
//    const IDLE = 2 << 0;
//    // 等待状态, 特定条件下会得到 回调/激活/退出
//    const WAITING = 2 << 1;
//    // 运行状态, 可以执行各种任务. 如果不进入等待状态, 会死
//    const RUNNABLE = 2 << 2;
//    // 死掉状态, 等待被垃圾回收.
//    const DEAD = 2 << 3;
//
//
//    // idle
//    const NEWBORN = 2 << 4 ^ self::IDLE;
//    const DYING = 2 << 5 ^ self::IDLE;
//
//    // waiting
//    const BLOCKING = 2 << 8 ^ self::WAITING;
//    const YIELDING = 2 << 9 ^ self::WAITING;
//    const SLEEPING = 2 << 10 ^ self::WAITING;
//    const DEPENDING = 2 << 11 ^ self::WAITING;
//    const AWAIT = 2 << 12 ^ self::WAITING;
//
//    // running
//    const ALIVE = 2 << 16 ^ self::RUNNABLE;
//
//    /*------- status -------*/
//
//    public function isStatus(int $statusCode) : bool;

    public function getContextId() : string;

    /**
     * 增加一个回调 ucl.
     *
     * @param string $ucl
     * @param string $fieldName
     */
    public function addCallbackUcl(string $ucl, string $fieldName) : void;

    /**
     * 取出所有的回调 ucl
     * @return string[]
     */
    public function popCallbackUcl() : array;

}