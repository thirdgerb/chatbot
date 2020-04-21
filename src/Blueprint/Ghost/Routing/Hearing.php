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

use Commune\Blueprint\Ghost\Callables\Operating;
use Commune\Blueprint\Ghost\Callables\Prediction;
use Commune\Blueprint\Ghost\Operator\Operator;

/**
 * Heed 常用的链式调用 API, 可以链式地定义复杂的对话逻辑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Hearing
{

    /**
     * 当 When 条件为真时, 执行 $operating 对象.
     * 链式调用存在时, 只会返回最初出现的 Operator 方法.
     *
     * @param callable|Prediction $when
     * @param callable|Operating $operating
     * @return Hearing
     */
    public function toDo(callable $operating, callable $when) : Hearing;

    /**
     * 只有在还没有生成 Operator 的时候才会执行
     * 返回结果应该是 null|Operator
     *
     * @param callable|Operating $operating
     * @return Hearing
     */
    public function action(callable $operating) : Hearing;

    /**
     * 无论是否已经生成了 Operator 都会执行.
     * 但有可能引起歧义.
     *
     * @param callable|Operating $operating
     * @return Hearing
     */
    public function always(callable $operating) : Hearing;


    /**
     * 结束流程.
     * @return Operator
     */
    public function end() : Operator;

    /**
     * 结束流程, 并且不使用全局的 fallback
     * @return Operator
     */
    public function privateEnd() : Operator;

    /**
     * 不执行 end 的各种fallback
     * 否则直接返回 已经获取的 Operator, 或者返回 Confuse
     */
    public function heardOrConfuse() : Operator;

    /**
     * 注册一系列的 Fallback, 在 End 时如果还没有 Operator, 就会执行.
     * @param callable ...$operating
     * @return Hearing
     */
    public function fallback(callable  ...$operating) : Hearing;

    /**
     * 执行自定义 Fallback, 只会运行一次.
     * @return Hearing
     */
    public function runFallback() : Hearing;

    /**
     * 执行系统 Fallback, 只会运行一次.
     * @return Hearing
     */
    public function runDefaultFallback() : Hearing;

}