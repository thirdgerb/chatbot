<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Event;

use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Framework\Session\SessionEvent;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class FinishRequest implements SessionEvent
{

    /**
     * @var Session
     */
    protected $session;

    /**
     * SessionStart constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }


    public function getEventName(): string
    {
        return static::class;
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }


}