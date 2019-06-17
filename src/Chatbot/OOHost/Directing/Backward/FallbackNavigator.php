<?php


namespace Commune\Chatbot\OOHost\Directing\Backward;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

abstract class FallbackNavigator extends AbsNavigator
{
    const EVENT = Definition::CANCEL;

    public function doDisplay() : ? Navigator
    {
        $history = $this->history;

        $callback = $this->history->getCurrentContext();
        // 一般的fallback, 自己的事件不会触发.
        // 只有fulfill 自己的事件会触发.
        while ($history = $history->intended()){
            $context = $history->getCurrentContext();
            $caller = $context->getDef();

            $navigator = $caller->onExiting(
                static::EVENT,
                $context,
                $this->dialog,
                $callback
            );

            if (isset($navigator)) {
                return $navigator;
            }

            $callback = $context;
        }

        return $this->then();
    }

    protected function then() : ? Navigator
    {
        $this->history->fallback();
        return $this->callbackCurrent();
    }

}