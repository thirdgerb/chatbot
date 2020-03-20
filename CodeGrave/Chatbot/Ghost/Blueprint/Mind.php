<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint;

use Commune\Chatbot\Ghost\Blueprint\Meta\Registrar;
use Commune\Chatbot\Ghost\Blueprint\Mind\ContextDef;

/**
 * 对话机器人的静态思维管理.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Mind
{

    /*----- commands -----*/

    public function hasCommand(string $commandName) : bool;



}