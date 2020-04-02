<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Events;

use Commune\Shell\Blueprint\Pipeline\ShellPipe;
use Commune\Shell\Blueprint\Event\ShellEvent;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StartShlPipe implements ShellEvent
{
    /**
     * @var ShellPipe
     */
    protected $pipe;

    /**
     * StartPipe constructor.
     * @param ShellPipe $pipe
     */
    public function __construct(ShellPipe $pipe)
    {
        $this->pipe = $pipe;
    }


    public function getId(): string
    {
        return static::class;
    }

    /**
     * @return ShellPipe
     */
    public function getPipe(): ShellPipe
    {
        return $this->pipe;
    }

}