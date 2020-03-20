<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 当前thread 进入sleep 状态.
 * 让出控制权给新的thread, 或者fallback 到最早sleep的一个thread
 */
class SleepTo extends AbsNavigator
{
    /**
     * @var Context|null
     */
    protected $to;


    /**
     * Redirector constructor.
     * @param Dialog $dialog
     * @param Context|null $to
     */
    public function __construct(
        Dialog $dialog,
        Context $to = null
    )
    {
        $this->to = $to;
        parent::__construct($dialog);
    }

    public function doDisplay(): ? Navigator
    {
        // 是否能够sleep to
        $history = $this->history->sleepTo($this->to);

        if (isset($history)) {
            return $this->startCurrent();
        }

        // 当 to 为null, 自己又是第一个的话, 就restart. 会导致不如预期.
        return $this->dialog->restart();
    }


}