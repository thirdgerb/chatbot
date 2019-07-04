<?php


namespace Commune\Chatbot\OOHost\Directing\Dialog;


use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Backward\Rewind;
use Commune\Chatbot\OOHost\Directing\Navigator;

class MissMatch extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        $missMatched = $this->dialog
            ->session
            ->chatbotConfig
            ->defaultMessages
            ->messageMissMatched;
        $this->dialog->say()->warning($missMatched);

        return new Rewind($this->dialog, $this->history);
    }
}