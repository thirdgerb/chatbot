<?php

/**
 * Class SubDialogImpl
 * @package Commune\Chatbot\OOHost\Dialogue
 */

namespace Commune\Chatbot\OOHost\Dialogue;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\History\History;
use Commune\Chatbot\OOHost\Session\Session;

class SubDialogImpl extends DialogImpl implements SubDialog
{
    /**
     * @var Dialog
     */
    protected $parent;

    /**
     * @var callable[]
     */
    protected $quitListeners = [];

    /**
     * @var callable[]
     */
    protected $missListeners = [];

    /**
     * @var callable[]
     */
    protected $waitListeners = [];

    public function __construct(Session $session, History $history, Dialog $parent, Message $message = null)
    {
        $this->parent = $parent;
        parent::__construct($session, $history, $message);
    }

    public function getParent(): Dialog
    {
        return $this->parent;
    }

    public function onQuit(callable $caller): void
    {
        $this->quitListeners[] = $caller;
    }

    public function fireQuit(): ? Navigator
    {
        return $this->callListeners($this->quitListeners);
    }

    protected function callListeners(array $listeners) : ? Navigator
    {
        if (empty($listeners)) {
            return null;
        }

        foreach ($listeners as $listener) {
            $result = $this->parent->app->callContextInterceptor(
                $this->parent->currentContext(),
                $listener
            );

            if ($result instanceof Navigator) {
                return $result;
            }
        }

        return null;
    }

    public function onMiss(callable $caller): void
    {
        $this->missListeners[] = $caller;
    }

    public function fireMiss(): ? Navigator
    {
        return $this->callListeners($this->missListeners);
    }

    public function onWait(callable $caller): void
    {
        $this->waitListeners[] = $caller;
    }

    public function fireWait(): ? Navigator
    {
        return $this->callListeners($this->waitListeners);
    }


}