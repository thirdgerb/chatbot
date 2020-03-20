<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Session;

use Commune\Chatbot\Ghost\Blueprint\Exceptions\TooManyRedirectsException;
use Commune\Chatbot\Ghost\Blueprint\Redirector;
use Commune\Chatbot\Ghost\Blueprint\Session\Tracker;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TrackerImpl implements Tracker
{
    /**
     * @var int
     */
    protected $ticks = 0;

    /**
     * @var int
     */
    protected $maxTicks;

    /**
     * TrackerImpl constructor.
     * @param int $maxTicks
     */
    public function __construct(int $maxTicks)
    {
        $this->maxTicks = $maxTicks;
    }

    public function record(Redirector $redirector): void
    {
        $this->tick();
    }


    /**
     * 对 Redirector 的运行个数进行技术.
     * 防止循环重定向发生.
     */
    protected function tick() : void
    {
        $this->ticks ++;
        if ($this->ticks > $this->maxTicks) {
            throw new TooManyRedirectsException(
                static::class
                . ' ticks '. $this->ticks
                . ' times more than max times '
                . $this->maxTicks
                . ', tracing:'
                . $this->brief()
            );
        }
    }

    public function brief(): string
    {
        // todo
    }


}