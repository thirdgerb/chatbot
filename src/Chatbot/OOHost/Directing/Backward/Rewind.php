<?php


namespace Commune\Chatbot\OOHost\Directing\Backward;


use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

class Rewind extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        $this->history->rewind();

        $question = $this->dialog->prevQuestion();
        if (isset($question)) {
            $this->dialog->reply($question);
        }

        return null;
    }


}