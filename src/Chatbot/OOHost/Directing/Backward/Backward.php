<?php


namespace Commune\Chatbot\OOHost\Directing\Backward;


use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Directing\Redirects\Home;

class Backward extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        $context = $this->history->getCurrentContext();
        $caller = $context->getDef();
        $navigator = $caller->onExiting(
            Definition::BACKWARD,
            $context,
            $this->dialog
        );

        if (isset($navigator)) {
            return $navigator;
        }

        $history = $this->history->backward();

        if (isset($history)) {
            $question = $history->currentQuestion();
            if (isset($question)) {
                $this->dialog->reply($question);
                return null;

            } else {
                return $this->dialog->repeat();
            }

        }

        return new Home($this->dialog, $this->history);
    }


}