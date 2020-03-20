<?php


namespace Commune\Chatbot\OOHost\Context\Stages;

use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class FallbackStageRoute extends AbsStageRoute
{

    public function __construct(string $name, Context $self, Dialog $dialog)
    {
        parent::__construct($name, $self, $dialog, null);
    }

    public function isStart(): bool
    {
        return false;
    }

    public function isIntended(): bool
    {
        return false;
    }


    public function isCallback(): bool
    {
        return false;
    }

    public function isFallback(): bool
    {
        return true;
    }

    public function defaultNavigator(): Navigator
    {
        return $this->navigator ?? $this->dialog->repeat();
    }


}