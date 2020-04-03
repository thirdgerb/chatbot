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
use Commune\Ghost\Blueprint\Pipeline\GhostPipe;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class EndGhostPipe implements GhostEvent
{
    /**
     * @var GhostPipe
     */
    protected $pipe;

    /**
     * StartGhtPipe constructor.
     * @param GhostPipe $pipe
     */
    public function __construct(GhostPipe $pipe)
    {
        $this->pipe = $pipe;
    }

    public function getId(): string
    {
        return static::class;
    }


    /**
     * @return GhostPipe
     */
    public function getPipe(): GhostPipe
    {
        return $this->pipe;
    }


}