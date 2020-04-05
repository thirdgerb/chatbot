<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Runtime;

use Commune\Framework\Blueprint\Session\Session;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Runtime
{

    public function getProcess() : Process;

    public function save(Session $session) : void;

}