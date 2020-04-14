<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Operators\Hear;

use Commune\Ghost\Blueprint\Operator\Operator;
use Commune\Ghost\Blueprint\Convo\Conversation;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StageOnHear extends AbsHear
{

    public function invoke(Conversation $conversation): ? Operator
    {
        return $this->routingPipes($conversation)
            ?? $this->routingStages($conversation)
            ?? $this->routingIntents($conversation)
            ?? $this->toChildProcess($conversation)
            ?? $this->heard($conversation);
    }

}