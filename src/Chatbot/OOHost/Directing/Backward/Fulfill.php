<?php


namespace Commune\Chatbot\OOHost\Directing\Backward;


use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Directing\Navigator;

class Fulfill extends FallbackNavigator
{
    public function doDisplay(): ? Navigator
    {
        $context = $this->history->getCurrentContext();
        if (!$this->skipSelfEvent) {
            $caller = $context->getDef();
            $navigator = $caller->callExiting(
                Definition::FULFILL,
                $context,
                $this->dialog
            );
            if (isset($navigator)) return $navigator;
        }

        $intended = $this->history->intended();
        if (isset($intended)) {
            return $this->intendToCurrent($context);
        }

        return $this->then();
    }

}