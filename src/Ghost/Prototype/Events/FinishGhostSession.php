<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Events;

use Commune\Ghost\Blueprint\Event\GhostEvent;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class FinishGhostSession implements GhostEvent
{
    public function getId(): string
    {
        return static::class;
    }


}