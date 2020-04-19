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

use Commune\Ghost\Blueprint\Operator\Operator;


/**
 * Context 语境向后回退.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Fallback
{
    /**
     * 拒绝用户访问, 取消当前 Thread. 可以被拦截.
     * @return Operator
     */
    public function reject() : Operator;

    /**
     * 取消当前 Thread. 可以被拦截.
     * @return Operator
     */
    public function cancel() : Operator;

    /**
     * 尝试退出当前会话, 可以被拦截.
     * @return Operator
     */
    public function quit() : Operator;

    /**
     * 完成当前 Context, 进行回调.
     * 会依次检查 DependOn 语境, blocking, sleeping
     * 如果没有任何回调方向了, 会导致 Quit
     *
     * @param int $gcTurn
     * @return Operator
     */
    public function fulfill(int $gcTurn = 0) : Operator;
}