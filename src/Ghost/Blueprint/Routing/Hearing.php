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

use Commune\Ghost\Blueprint\Match\ToDo;
use Commune\Ghost\Blueprint\Operator\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Hearing
{

    public function confuse() : Operator;

    public function dumb() : Operator;

    public function toDo(callable $action) : ToDo;

}