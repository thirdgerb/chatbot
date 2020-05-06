<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Dialog;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Routing\MoveOn;
use Commune\Blueprint\Ghost\Dialog\Routing\Withdrawing;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Withdraw extends
    Dialog,
    MoveOn,
    Withdrawing
{

}