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

use Commune\Blueprint\Ghost\Operator\Operator;

/**
 * 用户视角对话状态的回归.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Backward
{
    /**
     * 回到上一轮对话的终态
     *
     * @param bool $quiet   不需要重复消息.
     * @return Operator
     */
    public function rewind(bool $quiet) : Operator;

    /**
     * 返回若干步之前. 并且发送消息.
     *
     * @param int $steps
     * @return Operator
     */
    public function backStep(int $steps) : Operator;

}