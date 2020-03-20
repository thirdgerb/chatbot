<?php


namespace Commune\Chatbot\OOHost\Context\Stages;

use Commune\Chatbot\OOHost\Directing\Navigator;

class CallbackStageRoute extends AbsStageRoute
{
    public function isStart(): bool
    {
        return false;
    }

    public function isCallback(): bool
    {
        return true;
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
        if (isset($this->navigator)) {
            return $this->navigator;
        }
        return $this->dialog->missMatch();
    }


}