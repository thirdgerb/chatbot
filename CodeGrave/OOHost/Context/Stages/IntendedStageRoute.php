<?php


namespace Commune\Chatbot\OOHost\Context\Stages;

use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class IntendedStageRoute extends AbsStageRoute
{
    public function __construct(string $name, Context $self, Dialog $dialog, Context $value)
    {
        parent::__construct($name, $self, $dialog, $value);
    }

    public function isStart(): bool
    {
        return false;
    }

    public function isCallback(): bool
    {
        return false;
    }

    public function isFallback(): bool
    {
        return false;
    }

    public function isIntended(): bool
    {
        return true;
    }

    public function defaultNavigator(): Navigator
    {
        if (isset($this->navigator)) {
            return $this->navigator;
        }

        return $this->dialog->repeat();
    }

}