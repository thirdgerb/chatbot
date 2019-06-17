<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

class Home extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        $this->history->home();

        $context = $this->dialog->currentContext();
        $caller = $context->getDef();

        return $caller->callbackStage(
            $context,
            $this->dialog,
            $this->history->currentTask()->getStage(),
            null
        );
    }


}