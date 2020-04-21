<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

use Commune\Blueprint\Intercom\GhostInput;
use Commune\Blueprint\Intercom\GhostOutput;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Cloner
{

    public function handle(GhostInput $input) : GhostOutput;

}