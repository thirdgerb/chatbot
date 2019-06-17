<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Context\Listeners\HearingHandler;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * @mixin HearingHandler
 */
class FakeHearing
{

    /**
     * @var Navigator|null
     */
    protected $navigator;

    /**
     * @var Dialog
     */
    protected $dialog;

    /**
     * @var bool
     */
    protected $isStart;

    /**
     * FakeHearing constructor.
     * @param Navigator|null $navigator
     * @param Dialog $dialog
     * @param bool $isStart
     */
    public function __construct(
        ?Navigator $navigator,
        Dialog $dialog,
        bool $isStart
    )
    {
        $this->navigator = $navigator;
        $this->dialog = $dialog;
        $this->isStart = $isStart;
    }


    public function end(callable $fallback = null) : Navigator
    {
        if ($this->isStart) {
            return $this->navigator ?? $this->dialog->wait();
        }
        return $this->navigator ?? $this->dialog->missMatch();
    }

    public function __call($name, $arguments)
    {
        return $this;
    }
}