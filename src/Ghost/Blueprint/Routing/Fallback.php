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
interface Fallback
{
    /**
     * 拒绝用户访问
     *
     * @return Operator
     */
    public function reject() : Operator;

    /**
     * @return Operator
     */
    public function cancel() : Operator;

    /**
     * @return Operator
     */
    public function quit() : Operator;

    /**
     * @param int $gcTurn
     * @return Operator
     */
    public function fulfill(int $gcTurn = 0) : Operator;
}