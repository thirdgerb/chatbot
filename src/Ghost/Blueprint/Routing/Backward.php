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
     * @return Operator
     */
    public function rewind() : Operator;

    /**
     * 返回若干步之前.
     * @param int $steps
     * @return Operator
     */
    public function backStep(int $steps) : Operator;

    /**
     * 不说话
     * @return Operator
     */
    public function dumb() : Operator;
}