<?php


namespace Commune\Chatbot\OOHost\Directing\Stage;


use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Backward\Fulfill;
use Commune\Chatbot\OOHost\Directing\Navigator;

class NextStage extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        $nextStage = $this->history->nextStage();
        if (isset($nextStage)) {
            return $this->startCurrent();
        }

        return new Fulfill($this->dialog, $this->history);
    }

}