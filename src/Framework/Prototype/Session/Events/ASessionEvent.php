<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Session\Events;

use Commune\Framework\Blueprint\Session\SessionEvent;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ASessionEvent implements SessionEvent
{
    public function getId(): string
    {
        return static::class;
    }


}