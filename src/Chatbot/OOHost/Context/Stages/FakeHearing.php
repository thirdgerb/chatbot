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
     * FakeHearing constructor.
     * @param Dialog $dialog
     * @param Navigator|null $navigator
     */
    public function __construct(
        Dialog $dialog,
        Navigator $navigator
    )
    {
        $this->navigator = $navigator;
        $this->dialog = $dialog;
    }


    public function end(callable $fallback = null) : Navigator
    {
        return $this->navigator ?? $this->dialog->missMatch();
    }

    public function __call($name, $arguments)
    {
        return $this;
    }
}