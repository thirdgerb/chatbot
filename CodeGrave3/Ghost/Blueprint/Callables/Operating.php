<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Callables;

use Commune\Ghost\Blueprint\Operator\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Operating
{
    public function __invoke() : ? Operator;
}