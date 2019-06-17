<?php


namespace Commune\Chatbot\OOHost\Directing\Backward;


use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

class Fulfill extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        $context = $this->history->getCurrentContext();
        $caller = $context->getDef();
        $navigator = $caller->onExiting(
            Definition::FULFILL,
            $context,
            $this->dialog
        );

        if (isset($navigator)) {
            return $navigator;
        }

        $intended = $this->history->intended();
        if (isset($intended)) {
            return $this->callbackCurrent($context);
        }

        $this->history->fallback();
        return $this->callbackCurrent();
    }

}