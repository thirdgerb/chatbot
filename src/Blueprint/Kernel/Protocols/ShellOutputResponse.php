<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Kernel\Protocols;

use Commune\Protocols\HostMsg\IntentMsg;
use Commune\Protocols\IntercomMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellOutputResponse extends AppResponse, HasOutputs
{

    /**
     * @return IntercomMsg[]
     */
    public function getOutputs() : array;

    /**
     * @return IntentMsg[]
     */
    public function getIntents() : array;

    /**
     * @param IntentMsg[] $intents
     */
    public function setIntents(IntentMsg ...$intents) : void;
}