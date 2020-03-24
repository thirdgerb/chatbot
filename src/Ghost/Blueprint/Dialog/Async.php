<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Dialog;

use Commune\Ghost\Blueprint\Dialog;
use Commune\Ghost\Blueprint\Operator\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Async extends Dialog
{
    public function await() : Operator;

    public function retain() : Operator;

    public function drop() : Operator;

}