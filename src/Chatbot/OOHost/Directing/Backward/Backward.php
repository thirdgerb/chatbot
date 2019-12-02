<?php


namespace Commune\Chatbot\OOHost\Directing\Backward;


use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Directing\Reset\Rewind;

class Backward extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        $history = $this->history->backward();
        // 如果回溯的步骤不存在, 就只能 rewind 了
        if (!isset($history)) {
            return new Rewind($this->dialog);
        }

        // 只在回调的对象执行 onBackward 事件.
        $context = $this->history->getCurrentContext();
        $caller = $context->getDef();
        $navigator = $caller->callExiting(
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