<?php


namespace Commune\Chatbot\OOHost\Context\Stages;

use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class StartStageRoute extends AbsStageRoute
{
    public function __construct(string $name, Context $self, Dialog $dialog)
    {
        parent::__construct($name, $self, $dialog, null);
    }

    public function isStart(): bool
    {
        return true;
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
        return false;
    }

    public function defaultNavigator(): Navigator
    {
        return $this->navigator ?? $this->dialog->wait();
    }


}