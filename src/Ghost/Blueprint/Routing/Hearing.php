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
use Commune\Ghost\Blueprint\Routing\Hearing\When;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Hearing
{

    public function toDo(callable $action) : When;

    public function end() : Operator;

    public function dumb() : Operator;

    public function confuse() : Operator;
}