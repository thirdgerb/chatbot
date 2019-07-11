<?php


namespace Commune\Chatbot\OOHost\Directing\Backward;


use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

class Backward extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        $history = $this->history->backward();
        if (!isset($history)) {
            $history->home();
        }

        // 只在回调的对象会执行backward
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

        return $this->startCurrent();
    }


}