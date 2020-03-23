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
    public function reject() : Operator;

    public function cancel() : Operator;

    public function fulfill() : Operator;

    public function quit() : Operator;
}