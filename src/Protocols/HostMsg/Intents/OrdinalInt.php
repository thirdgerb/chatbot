<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocols\HostMsg\Intents;

use Commune\Protocols\HostMsg\IntentMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface OrdinalInt extends IntentMsg
{
    const ORDINAL_ENTITY_NAME = 'ordinal';

    public function getOrdinal() : int;
}