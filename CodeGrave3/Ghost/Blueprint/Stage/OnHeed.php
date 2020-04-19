<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Stage;

use Commune\Ghost\Blueprint\Callables\Operating;
use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Routing\Backward;
use Commune\Ghost\Blueprint\Routing\Fallback;
use Commune\Ghost\Blueprint\Routing\Hearing;
use Commune\Ghost\Blueprint\Routing\Staging;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 更多属性
 * @see Stage
 */
interface OnHeed extends Stage
{

    /**
     * 系统自定义的 End, 会根据全局的配置, 以及当前插入的配置, 尝试得到一个可用的 Operator
     * 如果没有任何一个 callable 对象给出合适的 Operator, 则会走 Confuse 流程.
     *
     * 通常用这个方法作为 Heed 的结束点.
     *
     * @return Operator
     */
    public function end() : Operator;


    /**
     * 当前 Stage 自定义的 ending, 不会加入系统全局的 ending.
     * 如果没有任何一个 callable 对象给出合适的 Operator, 则会走 Confuse 流程.
     *
     * @return Operator
     */
    public function privateEnd() : Operator;

    /**
     * 发呆. 通常遇到不需要处理的 Event 会这样.
     * 装作没听见, 不需要任何反应, 也不需要任何状态变更.
     * @return Operator
     */
    public function dumb() : Operator;

    /**
     * 没有命中任何可以处理的逻辑.
     * @return Operator
     */
    public function confuse() : Operator;

    /**
     * 处理复杂逻辑所用的链式调用.
     * 使用 to do api
     * @return Hearing
     */
    public function hearing() : Hearing;

    /**
     * 切换同一个 Context 下的 Stage
     * @return Staging
     */
    public function staging() : Staging;

    /**
     * 从当前 Context 回退.
     * @return Fallback
     */
    public function fallback() : Fallback;

    public function backward() : Backward;

}