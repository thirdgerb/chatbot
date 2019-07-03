<?php


namespace Commune\Chatbot\OOHost\Directing\Backward;


use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\History\History;

abstract class FallbackNavigator extends AbsNavigator
{
    const EVENT = Definition::CANCEL;

    /**
     * @var bool
     */
    protected $skipSelfEvent;

    public function __construct(
        Dialog $dialog,
        History $history,
        bool $skipSelfEvent = false
    )
    {
        $this->skipSelfEvent = $skipSelfEvent;
        parent::__construct($dialog, $history);
    }

    public function doDisplay() : ? Navigator
    {
        $history = $this->history;

        $context = $this->history->getCurrentContext();

        // 防止无限重定向
        if (!$this->skipSelfEvent) {
            $navigator = $context->getDef()->onExiting(
                static::EVENT,
                $context,
                $this->dialog
            );

            if (isset($navigator)) return $navigator;
        }

        $callback = $context;
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