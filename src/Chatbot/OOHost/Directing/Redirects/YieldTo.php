<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 将一个运行中的thread 挂起到 yielding. 只有服务回调才能唤醒.
 */
class YieldTo extends Redirector
{
    public function doDisplay(): ? Navigator
    {
        $this->history->yieldTo($this->to);
        return $this->startCurrent();
    }


}