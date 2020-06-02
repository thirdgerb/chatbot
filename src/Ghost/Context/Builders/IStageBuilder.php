<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Builders;

use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Framework\Spy\SpyAgency;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IStageBuilder implements StageBuilder
{
    /**
     * @var callable[]
     */
    protected $listeners = [];

    /**
     * @var callable
     */
    protected $redirector;

    public function __construct()
    {
        SpyAgency::incr(static::class);
    }

    public function onRedirect($caller): StageBuilder
    {
        $this->redirector = $caller;
        return $this;
    }

    public function onActivate($caller): StageBuilder
    {
        $this->listeners[Dialog\Activate::class] = $caller;
        return $this;
    }

    public function onReceive($caller): StageBuilder
    {
        $this->listeners[Dialog\Receive::class] = $caller;
        return $this;
    }

    public function onEvent(string $event, $caller): StageBuilder
    {
        $this->listeners[$event] = $caller;
        return $this;
    }


    public function fireRedirect(Dialog $prev, Dialog $current) : ? Operator
    {
        if (isset($this->redirector)) {
            return call_user_func($this->redirector, $prev, $current);
        }
        return null;
    }

    public function fire(Dialog $dialog) : ? Operator
    {
        foreach ($this->listeners as $type => $caller) {

            $operator = null;
            if (isset($caller) && is_a($dialog, $type, TRUE)) {
                $operator = $dialog->caller()->action($caller);
            }

            if (isset($operator)) {
                return $operator;
            }
        }

        return null;
    }

    public function __destruct()
    {
        $this->redirector = null;
        $this->listeners = [];
        SpyAgency::decr(static::class);
    }
}