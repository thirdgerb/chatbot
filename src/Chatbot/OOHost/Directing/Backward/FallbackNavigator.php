<?php


namespace Commune\Chatbot\OOHost\Directing\Backward;


use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

abstract class FallbackNavigator extends AbsNavigator
{
    const EVENT = Definition::CANCEL;

    /**
     * @var bool
     */
    protected $skipSelfEvent;

    public function __construct(
        Dialog $dialog,
        bool $skipSelfEvent = false
    )
    {
        $this->skipSelfEvent = $skipSelfEvent;
        parent::__construct($dialog);
    }

    public function doDisplay() : ? Navigator
    {
        $history = $this->history;

        $context = $this->history->getCurrentContext();

        // 防止无限重定向
        if (!$this->skipSelfEvent) {
            $navigator = $context->getDef()->callExiting(
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

            $navigator = $caller->callExiting(
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
        $history = $this->history->fallback();
        if (isset($history)) {
            return $this->fallbackCurrent();
        }

        // 如果不能fallback, 说明没有起点了, 就直接退出.
        // 测试一段时间.
        return new Quit($this->dialog);
    }

}