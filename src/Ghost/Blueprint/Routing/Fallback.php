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
use Commune\Message\Blueprint\Message;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Fallback
{
    /**
     * 拒绝访问
     * @param Message|null $message
     * @return Operator
     */
    public function reject(Message $message = null) : Operator;

    /**
     * @return Operator
     */
    public function cancel() : Operator;

    /**
     * @return Operator
     */
    public function quit() : Operator;
}