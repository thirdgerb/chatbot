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

use Commune\Blueprint\Ghost\Operator\Ending;
use Commune\Blueprint\Ghost\Operator\Operator;

/**
 * 用户视角对话状态的回归.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Hearing
{
    /**
     * 回到上一轮对话的终态
     *
     * @param bool $quiet  不需要重复上一轮的最终消息.
     * @return Ending
     */
    public function rewind(bool $quiet) : Ending;

    /**
     * 无法理解当前对话.
     * @return Operator
     */
    public function confuse() : Operator;

    /**
     * 重新激活当前 Stage.
     * @return Operator
     */
    public function reactivate() : Operator;

    /**
     * 返回若干步之前. 并且发送消息.
     *
     * @param int $steps
     * @return Ending
     */
    public function backStep(int $steps) : Ending;

    /**
     * 发呆, 不保留任何状态. 当作没有接受到消息.
     * @return Ending
     */
    public function dumb() : Ending;
}