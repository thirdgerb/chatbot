<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Convo;

use Commune\Blueprint\Ghost\Cloner;

/**
 * ConvoInstance 的替身.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ConvoStub
{

    public function toInstance(Cloner $cloner) : ConvoInstance;

}