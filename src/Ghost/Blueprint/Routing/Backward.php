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

    /**
     * 装作没听见, 不需要任何反应, 也不需要任何状态变更.
     * @return Operator
     */
    public function unheard() : Operator;
}