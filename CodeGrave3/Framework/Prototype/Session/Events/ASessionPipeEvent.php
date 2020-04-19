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

use Commune\Framework\Blueprint\Session\SessionPipe;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ASessionPipeEvent extends ASessionEvent
{
    /**
     * @var SessionPipe
     */
    protected $pipe;

    /**
     * ASessionPipeEvent constructor.
     * @param SessionPipe $pipe
     */
    public function __construct(SessionPipe $pipe)
    {
        $this->pipe = $pipe;
    }


    public function getPipe() : SessionPipe
    {
        return $this->pipe;
    }


}