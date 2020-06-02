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
     * @var Operator|null
     */
    public $operator;

    /**
     * @var Dialog
     */
    protected $dialog;

    /**
     * @var bool
     */
    protected $redirect;

    public function __construct(Dialog $dialog, bool $redirect)
    {
        $this->dialog = $dialog;
        $this->redirect = $redirect;

        SpyAgency::incr(static::class);
    }

    public function onRedirect($caller): StageBuilder
    {
        $prev = $this->dialog->prev;

        if ($this->redirect && isset($prev)) {
            $this->operator = $caller($prev, $this->dialog);
        }

        return $this;
    }

    public function onActivate($caller): StageBuilder
    {
        if ($this->dialog->isEvent(Dialog::ACTIVATE)) {
            $this->operator = $this->dialog->caller()->action($caller);
        }

        return $this;
    }

    public function onReceive($caller): StageBuilder
    {
        if ($this->dialog->isEvent(Dialog::RECEIVE)) {
            $this->operator = $this->dialog->caller()->action($caller);
        }

        return $this;
    }

    public function onResume($caller): StageBuilder
    {
        if ($this->dialog->isEvent(Dialog::RESUME)) {
            $this->operator = $this->dialog->caller()->action($caller);
        }

        return $this;
    }


    public function onEvent(string $event, $caller): StageBuilder
    {
        if ($this->dialog->isEvent($event)) {
            $this->operator = $this->dialog->caller()->action($caller);
        }

        return $this;
    }


    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }
}