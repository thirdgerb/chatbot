<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host\Contexts\CodeContext;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Exiting;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface OnWithdraw
{

    public function __withdraw(Exiting $dialog) : ? Dialog;

}